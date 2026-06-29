<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->required(),

                \Filament\Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->required(),

                \Filament\Forms\Components\TextInput::make('mobile_number')
                    ->label('Mobile Number')
                    ->tel()
                    ->required()
                    // THIS IS THE FIX: It checks the database before submitting.
                    // 'ignoreRecord: true' ensures it doesn't trigger an error against the user's OWN number when editing.
                    ->unique(ignoreRecord: true),

                \Filament\Forms\Components\Select::make('district')
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
                    ->searchable(),

                \Filament\Forms\Components\Textarea::make('address')
                    ->label('Home Address')
                    ->rows(3)
                    ->columnSpanFull(),

                \Filament\Forms\Components\TextInput::make('password')
                    ->password()
                    ->label('Reset User Password')
                    ->helperText('Type a new password here to reset it. Leave blank to keep the current password.')
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => \Illuminate\Support\Facades\Hash::make($state)),
            ]);
    }
}
