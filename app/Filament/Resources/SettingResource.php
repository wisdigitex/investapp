<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Resources\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('value')->nullable(),
            Forms\Components\TextInput::make('usdt_wallet')->label('USDT (TRC20) Wallet'),
            Forms\Components\TextInput::make('btc_wallet')->label('BTC Wallet'),
            Forms\Components\TextInput::make('eth_wallet')->label('ETH Wallet'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('key'),
            Tables\Columns\TextColumn::make('value')->limit(50),
            Tables\Columns\TextColumn::make('updated_at')->dateTime(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
