<?php

namespace App\Filament\Resources;

use App\Enums\RenewalStatus;
use App\Filament\Resources\RenewalResource\Pages;
use App\Models\Renewal;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RenewalResource extends Resource
{
    protected static ?string $model = Renewal::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $modelLabel = 'Renouvellement';

    protected static ?string $pluralModelLabel = 'Renouvellements';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('retiree_id')
                            ->label('Retraité')
                            ->relationship('retiree', 'full_name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('year')
                            ->label('Année')
                            ->placeholder(today()->year)
                            ->numeric()
                            ->integer()
                            ->minValue(1970)
                            ->maxValue(today()->year)
                            ->required(),
                        Select::make('status')
                            ->label('État')
                            ->options(RenewalStatus::class)
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('description')
                            ->columnSpanFull(),
                        Textarea::make('answer')
                            ->label('Réponse')
                            ->placeholder('Réponse')
                            ->columnSpanFull(),
                        FileUpload::make('documents')
                            ->label('Documents')
                            ->columnSpanFull()
                            ->multiple()
                            ->storeFileNamesIn('documents_names')
                            ->previewable(false)
                            ->downloadable()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('retiree.full_name')
                    ->label('Retraité')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Année')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('status')
                    ->label('État')
                    ->sortable()
                    ->searchable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->searchable()
                    ->sortable()
                    ->date('d-m-Y H:i')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRenewals::route('/'),
            'create' => Pages\CreateRenewal::route('/create'),
            'edit' => Pages\EditRenewal::route('/{record}/edit'),
        ];
    }
}
