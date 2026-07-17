<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Lesson;
use App\Models\Material;
use App\Services\ZoomService;

class CreateLiveClass extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-video-camera';
    protected static string | \UnitEnum | null $navigationGroup = 'Academic Management';
    protected static ?string $navigationLabel = 'Schedule Live Class';
    protected static ?string $title = 'Schedule Automated Zoom Class';

    protected string $view = 'filament.pages.create-live-class';

    public ?array $data = [];

    // NEW: Variable to hold the generated links for the web view
    public ?array $meetingDetails = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Section::make('Class Details')
                    ->description('This will automatically generate a Zoom meeting and attach it to the selected lesson.')
                    ->schema([
                        Select::make('lesson_id')
                            ->label('Target Lesson')
                            ->options(Lesson::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('title')
                            ->label('Class Title (e.g., 04 Paper - 08.30pm)')
                            ->required()
                            ->maxLength(255),

                        DateTimePicker::make('start_time')
                            ->label('Scheduled Date & Time')
                            ->native(false)
                            ->required(),

                        Select::make('duration')
                            ->label('Estimated Duration')
                            ->options([
                                60 => '1 Hour',
                                90 => '1.5 Hours',
                                120 => '2 Hours',
                                180 => '3 Hours',
                            ])
                            ->default(120)
                            ->required(),

                        Select::make('audience')
                            ->label('Access Audience')
                            ->options([
                                'all' => 'All Students (Free & Paid)',
                                'paid' => 'Paid (Online & Hall)',
                                'paid_hall' => 'Paid - Hall Only',
                            ])
                            ->default('all')
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function createClass()
    {
        $data = $this->form->getState();
        $zoomService = new ZoomService();

        try {
            // 1. Call Zoom API
            $zoomMeeting = $zoomService->createMeeting(
                $data['title'],
                $data['start_time'],
                $data['duration']
            );

            // 2. Save directly to your Materials table
            Material::create([
                'lesson_id'     => $data['lesson_id'],
                'type'          => 'Live',
                'title'         => $data['title'],
                'audience'      => $data['audience'],
                'zoom_url'      => $zoomMeeting['join_url'],
                'zoom_passcode' => $zoomMeeting['password'] ?? 'N/A',
                'link'          => $zoomMeeting['start_url'],
            ]);

            // 3. NEW: Save details to display on the screen
            $this->meetingDetails = [
                'title'     => $data['title'],
                'passcode'  => $zoomMeeting['password'] ?? 'N/A',
                'start_url' => $zoomMeeting['start_url'],
                'join_url'  => $zoomMeeting['join_url'],
            ];

            // 4. Reset form and notify success
            $this->form->fill();

            Notification::make()
                ->title('Live Class Created Successfully!')
                ->body('The Zoom meeting has been generated and published to students.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Zoom Integration Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
