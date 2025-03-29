<?php

namespace App\Filament\Resources;

use App\Enums\ClaimStatus;
use App\Enums\UserType;
use App\Filament\Resources\ClaimResource\Pages;
use App\Models\Claim;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ClaimResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Réclamation';

    protected static ?string $pluralModelLabel = 'Réclamations';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Textarea::make('description')
                    ->label('Déscription')
                    ->placeholder('Déscription')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('retiree.full_name')
                    ->label('Retraité')
                    ->searchable()
                    ->sortable()
                    ->hidden(Auth::user()->type == UserType::Retiree),
                TextColumn::make('status')
                    ->label('État')
                    ->sortable()
                    ->searchable()
                    ->badge(),
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),
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
                SelectFilter::make('status')
                    ->label('État')
                    ->options(ClaimStatus::class)
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Détails de la Réclamation')
                    ->extraModalFooterActions(function ($record) {

                        if (Auth::user()->type == UserType::Retiree) {
                            return [];
                        }

                        $updateStatus = function ($status) use ($record) {
                            $record->update(['status' => $status]);

                            return Notification::make()
                                ->success()
                                ->title('Succés.')
                                ->body('La réclamation a été mise à jour avec succés.')
                                ->send();
                        };

                        $treatedAction = Action::make('treat')
                            ->label('Traiter')
                            ->color('success')
                            ->action(fn () => $updateStatus(ClaimStatus::Treated))
                            ->cancelParentActions();

                        $rejectedAction = Action::make('markAsRejected')
                            ->label('Rejeter')
                            ->color('danger')
                            ->action(fn () => $updateStatus(ClaimStatus::Rejected))
                            ->cancelParentActions();

                        $pendingAction = Action::make('markAsPending')
                            ->label('En attente')
                            ->color('warning')
                            ->action(fn () => $updateStatus(ClaimStatus::Pending))
                            ->cancelParentActions();

                        return match ($record->status) {
                            ClaimStatus::Pending => [$treatedAction, $rejectedAction],
                            ClaimStatus::Treated => [$pendingAction, $rejectedAction],
                            ClaimStatus::Rejected => [$treatedAction, $pendingAction],
                            default => []
                        };
                    }),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Supprimer la Réclamation ?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(function ($query) {
                $user = Auth::user();
                if ($user->type == UserType::Retiree) {
                    $query->where('retiree_id', $user->retiree->id);
                }
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('retiree.full_name')
                    ->label('Retraité'),
                TextEntry::make('retiree.number')
                    ->label('Numéro d\'identification')
                    ->badge(),
                TextEntry::make('retiree.birthdate')
                    ->label('Date de naissance')
                    ->date('d-m-Y')
                    ->placeholder('Vide'),
                TextEntry::make('retiree.email')
                    ->label('Email')
                    ->placeholder('Vide'),
                TextEntry::make('retiree.phone')
                    ->label('Téléphone')
                    ->placeholder('Vide'),
                TextEntry::make('date')
                    ->label('Date de la réclamation')
                    ->date('d-m-Y H:i')
                    ->badge()
                    ->color('primary'),
                TextEntry::make('status')
                    ->label('État')
                    ->badge(),
                TextEntry::make('description')
                    ->columnSpanFull(),
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
            'index' => Pages\ListClaims::route('/'),
        ];
    }
}
