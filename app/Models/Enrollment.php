<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',            // Replaced student_name and phone
        'lesson_id',
        'payment_slip_path',  // Replaced bank_slip_url to match DB
        'status',
    ];

    /**
     * Get the user (student) associated with this enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson associated with this enrollment.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Check if the student can access the Live Class and PDFs.
     * Allowed: Free, Paid, Postpay.
     */
    public function canAccessBasicMaterials(): bool
    {
        return in_array($this->status, ['free', 'paid', 'postpay']);
    }

    /**
     * Check if the student can access Cloud Recordings.
     * Allowed: Paid only.
     */
    public function canAccessRecordings(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if the student is locked out and needs to pay/upload a slip.
     * Locked: Requested (or rejected).
     */
    public function isPendingPayment(): bool
    {
        return in_array($this->status, ['requested', 'rejected']);
    }
}
