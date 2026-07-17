<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use App\Models\User; 
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

// --- LAYOUT & SCHEMAS ---
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;

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
            
            Grid::make(2)->schema([
                Section::make('Student & Enrollment Information')
                    ->columnSpan(1)
                    ->components([
                        Select::make('user_id')
                            ->relationship('user', 'first_name')
                            ->label('Student')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),

                        Select::make('lesson_id')
                            ->relationship('lesson', 'name')
                            ->label('Lesson')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Payment Verification')
                    ->columnSpan(1)
                    ->components([
                        Placeholder::make('student_mobile')
                            ->label('Student Mobile Number')
                            ->content(function (Get $get) {
                                $userId = $get('user_id');
                                if (! $userId) {
                                    return new HtmlString('<span style="color: #6b7280;">Not selected</span>');
                                }
                                $user = User::find($userId);
                                return $user?->mobile_number 
                                    ? new HtmlString('<span style="font-weight: 600; color: #3b82f6;">' . $user->mobile_number . '</span>') 
                                    : new HtmlString('<span style="color: #6b7280;">No number available</span>');
                            }),

                        Select::make('status')
                            ->options([
                                'requested'       => 'Requested',
                                'pending_payment' => 'Pending Verification',
                                'postpay'         => 'Postpay',
                                'paid'            => 'Paid (Online)',
                                'paid_hall'       => 'Paid - Hall',
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
            ]),

            // --- AMENDED: User-Friendly Custom Styled Table ---
            Section::make('Previous Enrollments')
                ->description('History of classes this student has enrolled in.')
                ->columnSpanFull()
                ->components([
                    Placeholder::make('previous_enrollments_table')
                        ->hiddenLabel()
                        ->content(function (Get $get, ?Enrollment $record) {
                            $userId = $get('user_id');
                            
                            if (! $userId) {
                                return new HtmlString('<span style="color: #6b7280;">Select a student to view history.</span>');
                            }

                            $query = Enrollment::with('lesson')->where('user_id', $userId)->orderBy('created_at', 'desc');
                            if ($record) {
                                $query->where('id', '!=', $record->id);
                            }
                            $enrollments = $query->get();

                            if ($enrollments->isEmpty()) {
                                return new HtmlString('<span style="color: #6b7280; font-style: italic;">No previous enrollments found for this student.</span>');
                            }

                            // Built using inline styles to guarantee it works beautifully in Dark/Light mode
                            $html = '<div style="overflow-x: auto; border-radius: 0.5rem; border: 1px solid rgba(128, 128, 128, 0.2); margin-top: 0.5rem;">
                                        <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 0.875rem;">
                                            <thead style="background-color: rgba(128, 128, 128, 0.05);">
                                                <tr>
                                                    <th style="padding: 12px 16px; font-weight: 600; color: #9ca3af; border-bottom: 1px solid rgba(128, 128, 128, 0.2);">Class / Lesson</th>
                                                    <th style="padding: 12px 16px; font-weight: 600; color: #9ca3af; border-bottom: 1px solid rgba(128, 128, 128, 0.2);">Status</th>
                                                    <th style="padding: 12px 16px; font-weight: 600; color: #9ca3af; border-bottom: 1px solid rgba(128, 128, 128, 0.2);">Enrolled Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>';

                            foreach ($enrollments as $enr) {
                                $date = $enr->created_at ? $enr->created_at->format('M d, Y - h:i A') : 'N/A';
                                $className = $enr->lesson?->name ?? 'Unknown Class';
                                
                                // Colors mapped using RGBA to look great on dark backgrounds
                                $statusColors = [
                                    'requested'       => 'color: #eab308; background-color: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.2);',
                                    'pending_payment' => 'color: #3b82f6; background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);',
                                    'paid'            => 'color: #22c55e; background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2);',
                                    'paid_hall'       => 'color: #22c55e; background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2);',
                                    'free'            => 'color: #22c55e; background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2);',
                                    'postpay'         => 'color: #9ca3af; background-color: rgba(156, 163, 175, 0.1); border: 1px solid rgba(156, 163, 175, 0.2);',
                                ];
                                $colorStyle = $statusColors[$enr->status] ?? 'color: #9ca3af; background-color: rgba(156, 163, 175, 0.1); border: 1px solid rgba(156, 163, 175, 0.2);';
                                $statusLabel = ucfirst(str_replace('_', ' ', $enr->status));

                                $html .= "<tr>
                                            <td style='padding: 12px 16px; border-bottom: 1px solid rgba(128, 128, 128, 0.1); font-weight: 500;'>{$className}</td>
                                            <td style='padding: 12px 16px; border-bottom: 1px solid rgba(128, 128, 128, 0.1);'>
                                                <span style='padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: inline-block; {$colorStyle}'>
                                                    {$statusLabel}
                                                </span>
                                            </td>
                                            <td style='padding: 12px 16px; border-bottom: 1px solid rgba(128, 128, 128, 0.1); color: #9ca3af;'>{$date}</td>
                                          </tr>";
                            }

                            $html .= '</tbody></table></div>';

                            return new HtmlString($html);
                        }),
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

                TextColumn::make('total_enrollments')
                    ->label('Enrollments')
                    ->getStateUsing(fn ($record) => Enrollment::where('user_id', $record->user_id)->count())
                    ->alignCenter(),

                TextColumn::make('student_status')
                    ->label('Student Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if (! $record->user_id) return 'Unknown';

                        $totalEnrollments = Enrollment::where('user_id', $record->user_id)->count();

                        if ($totalEnrollments <= 1) {
                            return 'New';
                        }

                        $recentEnrollments = Enrollment::where('user_id', $record->user_id)
                            ->where('created_at', '>=', Carbon::now()->subMonth())
                            ->count();

                        if ($recentEnrollments > 0) {
                            return 'Regular';
                        }

                        return 'Not Regular';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'New'         => 'info',
                        'Regular'     => 'success',
                        'Not Regular' => 'danger',
                        default       => 'gray',
                    }),

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
            ->recordActions([ 
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