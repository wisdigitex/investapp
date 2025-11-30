<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileLevelResource\Pages;
use App\Models\ProfileLevel;
use Filament\Forms;
use Filament\Resources\Form;      // ✔ CORRECT FILAMENT v2
use Filament\Resources\Resource;  // ✔
use Filament\Resources\Table;     // ✔
use Filament\Tables;

class ProfileLevelResource extends Resource
{
    protected static ?string $model = ProfileLevel::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('upgrade_price')->numeric(),
            Forms\Components\TextInput::make('min_withdrawal')->numeric(),
            Forms\Components\TextInput::make('max_daily_withdrawals')->numeric(),
            Forms\Components\Toggle::make('requires_referrals'),
            Forms\Components\TextInput::make('min_referrals')->numeric(),
            Forms\Components\Textarea::make('unlocks')->json(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('upgrade_price'),
            Tables\Columns\TextColumn::make('min_withdrawal'),
            Tables\Columns\TextColumn::make('max_daily_withdrawals'),
            Tables\Columns\IconColumn::make('requires_referrals')->boolean(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfileLevels::route('/'),
            'create' => Pages\CreateProfileLevel::route('/create'),
            'edit' => Pages\EditProfileLevel::route('/{record}/edit'),
        ];
    }
}
