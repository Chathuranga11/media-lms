<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

// --- LAYOUT & SCHEMAS ---
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

// --- FORMS ---
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;

// --- TABLE ---
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class EnrollmentResource extends Resource
{
    public static function getModel(): string
    {
        return Enrollment::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return 'student_name';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Student & Enrollment Information')
                ->components([
                    Select::make('user_id')
                        ->relationship('user', 'first_name')
                        ->label('Student')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Select::make('lesson_id')
                        ->relationship('lesson', 'name')
                        ->label('Lesson')
                        ->required()
                        ->searchable()
                        ->preload(),
                ])->columns(2),

            Section::make('Payment Verification')
                ->components([
                    Select::make('status')
                        ->options([
                            'requested'       => 'Requested',
                            'pending_payment' => 'Pending Verification',
                            'postpay'         => 'Postpay',
                            'paid'            => 'Paid (Online)',
                            'paid_hall'       => 'Paid - Hall', // <-- Added Paid Hall
                            'free'            => 'Free',
                        ])
                        ->required()
                        ->default('requested'),

                    FileUpload::make('payment_slip_path')
                        ->label('Payment Slip')
                        ->disk('public')
                        ->directory('payment-slips')
                        ->image()
                        ->openable()
                        ->downloadable()
                        ->deletable(false)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.first_name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.mobile_number')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('lesson.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'requested',
                        'info'    => 'pending_payment',
                        'gray'    => 'postpay',
                        'success' => fn($state) => in_array($state, ['paid', 'free', 'paid_hall']),
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'requested'       => 'Requested',
                        'pending_payment' => 'Pending Verification',
                        'postpay'         => 'Postpay',
                        'paid'            => 'Paid (Online)',
                        'paid_hall'       => 'Paid - Hall',
                        'free'            => 'Free',
                    ]),
            ])
            ->recordActions([ // RESTORED exactly to your original method
                \Filament\Actions\Action::make('approvePayment')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => in_array($record->status, ['requested', 'pending_payment']))
                    ->form([
                        Placeholder::make('bank_slip')
                            ->label('Uploaded Bank Slip')
                            ->content(function ($record) {
                                $slipPath = $record->payment_slip_path;

                                if (!$slipPath) {
                                    return new HtmlString('<span class="text-danger-500 font-medium">No slip uploaded.</span>');
                                }

                                return new HtmlString('<img src="' . asset('storage/' . $slipPath) . '" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);" />');
                            }),
                    ])
                    ->modalSubmitActionLabel('Approve & Unlock Access')
                    ->modalHeading('Review Payment')
                    ->action(function ($record) {
                        $record->update(['status' => 'paid']);
                    }),

                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
