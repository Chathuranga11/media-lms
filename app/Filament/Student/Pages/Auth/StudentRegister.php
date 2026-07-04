<?php

namespace App\Filament\Student\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea; // <-- 1. Import this
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Register as BaseRegister;

class StudentRegister extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        $years = array_combine(range(2026, 2030), range(2026, 2030));

        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required(),

                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required(),

                TextInput::make('mobile_number')
                    ->label('Mobile Number')
                    ->required()
                    ->tel()
                    ->unique($this->getUserModel())
                    ->validationAttribute('Mobile Number'),

                Select::make('district')
                    ->label('District')
                    ->options([
                        'Ampara' => 'Ampara',
                        'Anuradhapura' => 'Anuradhapura',
                        'Badulla' => 'Badulla',
                        'Batticaloa' => 'Batticaloa',
                        'Colombo' => 'Colombo',
                        'Galle' => 'Galle',
                        'Gampaha' => 'Gampaha',
                        'Hambantota' => 'Hambantota',
                        'Jaffna' => 'Jaffna',
                        'Kalutara' => 'Kalutara',
                        'Kandy' => 'Kandy',
                        'Kegalle' => 'Kegalle',
                        'Kilinochchi' => 'Kilinochchi',
                        'Kurunegala' => 'Kurunegala',
                        'Mannar' => 'Mannar',
                        'Matale' => 'Matale',
                        'Matara' => 'Matara',
                        'Monaragala' => 'Monaragala',
                        'Mullaitivu' => 'Mullaitivu',
                        'Nuwara Eliya' => 'Nuwara Eliya',
                        'Polonnaruwa' => 'Polonnaruwa',
                        'Puttalam' => 'Puttalam',
                        'Ratnapura' => 'Ratnapura',
                        'Trincomalee' => 'Trincomalee',
                        'Vavuniya' => 'Vavuniya',
                    ])
                    ->searchable()
                    ->required(),

                Select::make('al_batch')
                    ->label('A/L Batch (Year)')
                    ->options($years)
                    ->required()
                    ->native(false)
                    ->searchable(),

                Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
                    ->required()
                    ->native(false),

                // 2. Added Home Address field
                Textarea::make('address')
                    ->label('Home Address (For Tute Delivery')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ])
            ->statePath('data');
    }
}
