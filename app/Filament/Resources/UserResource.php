<?php

namespace App\Filament\Resources;

use App\Enums\UserType;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de connexion')
                    ->columns(2)
                    ->schema([
                        TextInput::make('identifier')
                            ->label('Identifiant')
                            ->placeholder('Identifiant')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record),
                        Select::make('type')
                            ->label('Type')
                            ->options(UserType::class)
                            ->disabledOn('edit')
                            ->required()
                            ->live(),
                        TextInput::make('password')
                            ->label('Nouveau mot de passe')
                            ->placeholder('Nouveau mot de passe')
                            ->password()
                            ->revealable()
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state)),
                    ]),
                Section::make('Informations du retraité')
                    ->columns(2)
                    ->relationship('retiree')
                    ->visible(fn ($get) => $get('type') == UserType::Retiree->value)
                    ->schema([
                        TextInput::make('last_name')
                            ->label('Nom')
                            ->placeholder('Nom')
                            ->required(),
                        TextInput::make('first_name')
                            ->label('Prénom')
                            ->placeholder('prénom')
                            ->required(),
                        TextInput::make('number')
                            ->label('Numéro d\'identification')
                            ->placeholder('Numéro d\'identification')
                            ->unique(ignorable: fn ($record) => $record)
                            ->required(),
                        DatePicker::make('birthdate')
                            ->label('Date de naissance')
                            ->placeholder('Date de naissance'),
                        TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Email'),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->placeholder('Phone'),
                    ]),
                Section::make('Informations de l\'agent')
                    ->columns(2)
                    ->relationship('agent')
                    ->visible(fn ($get) => $get('type') == UserType::Agent->value)
                    ->schema([
                        TextInput::make('last_name')
                            ->label('Nom')
                            ->placeholder('Nom')
                            ->required(),
                        TextInput::make('first_name')
                            ->label('Prénom')
                            ->placeholder('prénom')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('identifier')
                    ->label('Identifiant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(UserType::class)
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(fn (Builder $query) => $query->where('type', '!=', UserType::Admin));
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
