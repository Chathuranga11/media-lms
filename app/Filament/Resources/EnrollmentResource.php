<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

// --- LAYOUT ---
use Filament\Schemas\Components\Section;

// --- FORMS ---
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

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
                            'requested' => 'Requested',
                            'paid'      => 'Paid',
                            'free'      => 'Free',
                            'postpay'   => 'Postpay',
                        ])
                        ->required()
                        ->default('requested'),

                    FileUpload::make('payment_slip_path')
                        ->label('Payment Slip')
                        ->disk('public')              // <-- CRITICAL FIX: Routes to storage/app/public/
                        ->directory('payment-slips')
                        ->image()
                        ->openable()                  // <-- Added: Click thumbnail to open full photo
                        ->downloadable()              // <-- Added: Download for accounting
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
                        'success' => fn($state) => in_array($state, ['paid', 'free', 'postpay']),
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'requested' => 'Requested',
                        'free'      => 'Free',
                        'paid'      => 'Paid',
                        'postpay'   => 'Postpay',
                    ]),
            ])
            ->recordActions([

                \Filament\Actions\Action::make('approvePayment')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'requested')
                    ->form([
                        \Filament\Forms\Components\Placeholder::make('bank_slip')
                            ->label('Uploaded Bank Slip')
                            ->content(function ($record) {
                                $slipPath = $record->payment_slip_path;

                                if (!$slipPath) {
                                    return new \Illuminate\Support\HtmlString('<span class="text-danger-500 font-medium">No slip uploaded.</span>');
                                }

                                return new \Illuminate\Support\HtmlString('<img src="' . asset('storage/' . $slipPath) . '" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);" />');
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
