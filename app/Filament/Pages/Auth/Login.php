<?php

namespace App\Filament\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // By placing TextInput directly here, we avoid all return-type errors!
                TextInput::make('mobile_number')
                    ->label('Mobile Number')
                    ->required()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                    
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'mobile_number' => $data['mobile_number'],
            'password'  => $data['password'],
        ];
    }
}