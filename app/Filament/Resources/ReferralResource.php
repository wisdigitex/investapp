<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralResource\Pages;
use App\Models\Referral;
use Filament\Forms;
use Filament\Resources\Form;      // ✔ Correct import for v2
use Filament\Resources\Resource;  // ✔
use Filament\Resources\Table;     // ✔
use Filament\Tables;

class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('referrer_id')
                ->relationship('referrer', 'email')
                ->searchable(),

            Forms\Components\Select::make('referred_id')
                ->relationship('referred', 'email')
                ->searchable(),

            Forms\Components\TextInput::make('bonus_amount')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'credited' => 'Credited',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),

            Tables\Columns\TextColumn::make('referrer.email')
                ->label('Referrer'),

            Tables\Columns\TextColumn::make('referred.email')
                ->label('Referred User'),

            Tables\Columns\TextColumn::make('bonus_amount'),

            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'credited',
                ]),

            Tables\Columns\TextColumn::make('created_at')->date(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferrals::route('/'),
            'create' => Pages\CreateReferral::route('/create'),
            'edit' => Pages\EditReferral::route('/{record}/edit'),
        ];
    }
}
