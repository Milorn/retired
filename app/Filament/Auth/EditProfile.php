<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getIdentifierFormComponent(),
                $this->getPasswordFormComponent()
                    ->placeholder('Mot de passe'),
                $this->getPasswordConfirmationFormComponent()
                    ->placeholder('Confirmation'),
            ])
            ->statePath('data');
    }

    protected function getIdentifierFormComponent(): Component
    {
        return TextInput::make('identifier')
            ->label('Identifiant')
            ->required()
            ->unique(ignorable: fn ($record) => $record)
            ->autocomplete()
            ->autofocus()
            ->placeholder('Identifiant')
            ->extraInputAttributes(['tabindex' => 1]);
    }
}
