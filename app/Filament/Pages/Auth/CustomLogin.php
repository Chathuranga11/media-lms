<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;

class CustomLogin extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('phone')
                ->label('Mobile Number')
                ->required()
                ->autofocus(),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(),
        ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'mobile_number' => $data['phone'],
            'password'      => $data['password'],
        ];
    }

    // This method physically stops disabled users from logging in
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if (! auth()->user()->is_active) {
            auth()->logout();

            throw ValidationException::withMessages([
                'data.phone' => 'Deactivated user, please contact Administrator.',
            ]);
        }

        return $response;
    }
}
