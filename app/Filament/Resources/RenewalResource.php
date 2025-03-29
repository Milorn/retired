<?php

namespace App\Filament\Resources;

use App\Enums\RenewalStatus;
use App\Enums\UserType;
use App\Filament\Resources\RenewalResource\Pages;
use App\Models\Renewal;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RenewalResource extends Resource
{
    protected static ?string $model = Renewal::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $modelLabel = 'Renouvellement';

    protected static ?string $pluralModelLabel = 'Renouvellements';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return Auth::user()->type == UserType::Retiree ? 'Mes Renouvellements' : 'Renouvellements';
    }

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
                            ->required()
                            ->disabled()
                            ->visible(Auth::user()->type != UserType::Retiree),
                        TextInput::make('year')
                            ->label('Année')
                            ->placeholder(today()->year)
                            ->numeric()
                            ->integer()
                            ->minValue(1970)
                            ->maxValue(today()->year)
                            ->required()
                            ->disabled()
                            ->visible(Auth::user()->type != UserType::Retiree),
                        Select::make('status')
                            ->label('État')
                            ->options(RenewalStatus::class)
                            ->required()
                            ->visible(Auth::user()->type != UserType::Retiree)
                            ->live(),
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('description')
                            ->columnSpanFull(),
                        Textarea::make('answer')
                            ->label('Motif du refus')
                            ->placeholder('Réponse')
                            ->columnSpanFull()
                            ->visible(fn ($operation, $get) => $operation == 'edit' && $get('status') == RenewalStatus::Rejected->value)
                            ->required(),
                        FileUpload::make('documents')
                            ->label('Documents')
                            ->columnSpanFull()
                            ->multiple()
                            ->storeFileNamesIn('documents_names')
                            ->previewable(false)
                            ->downloadable()
                            ->required()
                            ->disabledOn('edit')
                            ->acceptedFileTypes(['application/pdf']),
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
                    ->sortable()
                    ->visible(Auth::user()->type != UserType::Retiree),
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
                    ->label('Date de la demande')
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
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Détails de la demande')
                    ->extraModalFooterActions(function ($record) {

                        if (Auth::user()->type == UserType::Retiree) {
                            return [];
                        }

                        $updateStatus = function ($status, $answer = null) use ($record) {
                            $record->update(['status' => $status, 'answer' => $answer ?? $record->answer]);

                            return Notification::make()
                                ->success()
                                ->title('Succés.')
                                ->body('La demande a été mise à jour avec succés.')
                                ->send();
                        };

                        $treatedAction = Action::make('treat')
                            ->label('Valider')
                            ->color('success')
                            ->action(fn () => $updateStatus(RenewalStatus::Done))
                            ->cancelParentActions();

                        $rejectedAction = Action::make('markAsRejected')
                            ->label('Rejeter')
                            ->color('danger')
                            ->form([
                                Textarea::make('answer')
                                    ->label('Motif du refus')
                                    ->placeholder('Motif du refus')
                                    ->columnSpanFull()
                                    ->required(),
                            ])
                            ->action(fn ($data) => $updateStatus(RenewalStatus::Rejected, $data['answer']))
                            ->modalHeading('Rejeter la demande')
                            ->cancelParentActions();

                        $pendingAction = Action::make('markAsPending')
                            ->label('En attente')
                            ->color('warning')
                            ->action(fn () => $updateStatus(RenewalStatus::Pending))
                            ->cancelParentActions();

                        return match ($record->status) {
                            RenewalStatus::Pending => [$treatedAction, $rejectedAction],
                            RenewalStatus::Done => [$pendingAction, $rejectedAction],
                            RenewalStatus::Rejected => [$treatedAction, $pendingAction],
                            default => []
                        };
                    }),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Supprimer la demande'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(function ($query) {
                if (Auth::user()->type == UserType::Retiree) {
                    $query->where('retiree_id', Auth::user()->retiree->id);
                }
            })->defaultSort('updated_at', 'desc');
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
                TextEntry::make('created_at')
                    ->label('Date de la demande')
                    ->date('d-m-Y H:i')
                    ->badge()
                    ->color('primary'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label('État')
                    ->badge(),
                TextEntry::make('answer')
                    ->label('Motif du refus')
                    ->visible(fn ($record) => $record->status == RenewalStatus::Rejected)
                    ->columnSpanFull(),
                TextEntry::make('documents')
                    ->label('Documents')
                    ->listWithLineBreaks()
                    ->formatStateUsing(function ($state, $record) {
                        return sprintf('<span style="color: oklch(0.623 0.214 259.815)" class="text-xs rounded-md mx-1 font-medium px-2 min-w-[theme(spacing.6)] py-1  bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30"> <a href="%s"  target="_blank">%s</a></span>', '/storage/'.$state, $record->documents_names[$state]);
                    })->html()
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
            'index' => Pages\ListRenewals::route('/'),
            'create' => Pages\CreateRenewal::route('/create'),
            // 'edit' => Pages\EditRenewal::route('/{record}/edit'),
        ];
    }
}
