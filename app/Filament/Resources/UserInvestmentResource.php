<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserInvestmentResource\Pages;
use App\Models\UserInvestment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Resources\Table;

class UserInvestmentResource extends Resource
{
    protected static ?string $model = UserInvestment::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Investments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'email')->searchable(),
            Forms\Components\Select::make('package_id')->relationship('package', 'name')->searchable(),
            Forms\Components\TextInput::make('amount')->numeric(),
            Forms\Components\TextInput::make('expected_payout')->numeric(),
            Forms\Components\DatePicker::make('start_date'),
            Forms\Components\DatePicker::make('end_date'),
            Forms\Components\Select::make('status')
                ->options([
                    'ongoing' => 'Ongoing',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('user.email'),
            Tables\Columns\TextColumn::make('package.name'),
            Tables\Columns\TextColumn::make('amount'),
            Tables\Columns\TextColumn::make('expected_payout'),
            Tables\Columns\TextColumn::make('start_date')->date(),
            Tables\Columns\TextColumn::make('end_date')->date(),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'ongoing',
                    'success' => 'completed',
                    'danger' => 'cancelled',
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserInvestments::route('/'),
            'edit' => Pages\EditUserInvestment::route('/{record}/edit'),
        ];
    }
}
