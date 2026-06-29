<?php

namespace App\Filament\Student\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Split into First and Last Name
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required(),

                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required(),

                // Updated to 'mobile_number'
                TextInput::make('mobile_number')
                    ->label('Mobile Number')
                    ->required()
                    ->tel()
                    ->unique(ignoreRecord: true),

                TextInput::make('al_batch')
                    ->label('A/L Batch')
                    ->numeric(),

                Textarea::make('address')
                    ->label('Home Address')
                    ->rows(3),
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
                    ->searchable() // Allows users to type and search
                    ->required(),

                Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
                    ->native(false),

                DatePicker::make('dob')
                    ->label('Date of Birth')
                    ->displayFormat('Y-m-d')
                    ->native(false),

                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
