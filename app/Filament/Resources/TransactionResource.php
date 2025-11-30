<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Resources\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'email'),
            Forms\Components\Select::make('type')->options([
                'deposit' => 'Deposit',
                'withdrawal' => 'Withdrawal',
                'profit' => 'Profit',
                'referral_bonus' => 'Referral Bonus',
                'adjustment' => 'Adjustment',
                'upgrade_purchase' => 'Upgrade Purchase',
            ]),
            Forms\Components\TextInput::make('amount')->numeric(),
            Forms\Components\TextInput::make('currency'),
            Forms\Components\TextInput::make('wallet_address'),
            Forms\Components\TextInput::make('tx_hash'),
            Forms\Components\Select::make('status')->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('user.email'),
            Tables\Columns\BadgeColumn::make('type')->colors([
                'info' => 'deposit',
                'warning' => 'withdrawal',
                'success' => 'profit',
                'primary' => 'referral_bonus',
            ]),
            Tables\Columns\TextColumn::make('amount'),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'warning' => 'pending',
                'success' => 'approved',
                'danger' => 'rejected',
            ]),
            Tables\Columns\TextColumn::make('tx_hash'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),

            // APPROVE DEPOSIT OR WITHDRAWAL
            Tables\Actions\Action::make('approve')
                ->visible(fn ($record) => $record->status === 'pending')
                ->color('success')
                ->requiresConfirmation()
                ->action(function ($record) {

                    // Mark as approved
                    $record->status = 'approved';
                    $record->save();

                    $user = $record->user;

                    // 📌 DEPOSIT → ADD TO MAIN BALANCE
                    if ($record->type === 'deposit') {
                        $user->main_balance += $record->amount;
                        $user->save();
                    }

                    // 📌 WITHDRAWAL → DEDUCT FROM EARNINGS BALANCE
                    if ($record->type === 'withdrawal') {
                        if ($user->earnings_balance >= $record->amount) {
                            $user->earnings_balance -= $record->amount;
                        } else {
                            // fallback if wrong balance
                            $user->main_balance -= $record->amount;
                        }
                        $user->save();
                    }

                    Notification::make()
                        ->success()
                        ->title('Transaction approved')
                        ->send();
                }),

            // REJECT ANY TRANSACTION
            Tables\Actions\Action::make('reject')
                ->visible(fn ($record) => $record->status === 'pending')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->status = 'rejected';
                    $record->save();

                    Notification::make()
                        ->danger()
                        ->title('Transaction rejected')
                        ->send();
                }),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
