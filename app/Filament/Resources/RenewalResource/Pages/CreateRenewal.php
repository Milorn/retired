<?php

namespace App\Filament\Resources\RenewalResource\Pages;

use App\Enums\RenewalStatus;
use App\Filament\Resources\RenewalResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRenewal extends CreateRecord
{
    protected static string $resource = RenewalResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Nouvelle demande';

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Ajouter');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['retiree_id'] = Auth::user()->retiree->id;
        $data['status'] = RenewalStatus::Pending;
        $data['year'] = today()->year;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Demande ajoutée avec succés.';
    }
}
