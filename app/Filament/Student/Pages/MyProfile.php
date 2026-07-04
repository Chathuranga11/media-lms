<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;

// --- LAYOUT COMPONENTS ---
use Filament\Schemas\Components\Section;

// --- INPUT COMPONENTS ---
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user';
    protected string $view = 'filament.student.pages.my-profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('first_name')->label('First Name')->required(),
                        TextInput::make('last_name')->label('Last Name')->required(),
                        TextInput::make('mobile_number')->label('Mobile Number')->tel()->required(),

                        Select::make('gender')
                            ->label('Gender')
                            ->options(['Male' => 'Male', 'Female' => 'Female'])
                            ->native(false),

                        // MOVED THIS INSIDE THE SECTION
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

                        TextInput::make('al_batch')->label('A/L Batch (Year)')->numeric(),
                        DatePicker::make('dob')->label('Date of Birth')->displayFormat('Y-m-d')->native(false),

                        Textarea::make('address')
                            ->label('Home Address')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2), // SECTION ENDS HERE

                Section::make('Update Password')
                    ->description('Leave blank if you do not want to change your password.')
                    ->schema([
                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state)),

                        TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->same('new_password')
                            ->requiredWith('new_password'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (empty($data['new_password'])) {
            unset($data['new_password']);
            unset($data['new_password_confirmation']);
        } else {
            $data['password'] = $data['new_password'];
            unset($data['new_password']);
            unset($data['new_password_confirmation']);
        }

        Auth::user()->update($data);

        Notification::make()
            ->success()
            ->title('Profile updated successfully')
            ->send();
    }
}
