<?php

namespace App\Filament\Resources\ClaimResource\Pages;

use App\Enums\ClaimStatus;
use App\Filament\Resources\ClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListClaims extends ListRecords
{
    protected static string $resource = ClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle Réclamation')
                ->modalHeading('Nouvelle Réclamation')
                ->createAnother(false)
                ->modalSubmitActionLabel('Ajouter')
                ->mutateFormDataUsing(function ($data) {
                    $data['retiree_id'] = Auth::user()->retiree->id;
                    $data['status'] = ClaimStatus::Pending;
                    $data['date'] = today();

                    return $data;
                }),

        ];
    }
}
