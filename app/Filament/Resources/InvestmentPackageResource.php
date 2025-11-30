<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvestmentPackageResource\Pages;
use App\Models\InvestmentPackage;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class InvestmentPackageResource extends Resource
{
    protected static ?string $model = InvestmentPackage::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Investments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('category'),
            Forms\Components\TextInput::make('min_amount')->numeric(),
            Forms\Components\TextInput::make('max_amount')->numeric(),
            Forms\Components\TextInput::make('duration_days')->numeric(),
            Forms\Components\TextInput::make('roi_percent')->numeric(),
            Forms\Components\Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('category'),
            Tables\Columns\TextColumn::make('min_amount'),
            Tables\Columns\TextColumn::make('max_amount'),
            Tables\Columns\TextColumn::make('duration_days'),
            Tables\Columns\TextColumn::make('roi_percent'),
            Tables\Columns\IconColumn::make('active')->boolean(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvestmentPackages::route('/'),
            'create' => Pages\CreateInvestmentPackage::route('/create'),
            'edit' => Pages\EditInvestmentPackage::route('/{record}/edit'),
        ];
    }
}
