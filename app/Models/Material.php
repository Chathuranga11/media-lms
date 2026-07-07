<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    // This tells Laravel's security bouncer to let these specific form fields pass through!
    protected $fillable = [
        'lesson_id',
        'title',
        'type',
        'audience',
        'zoom_url',
        'zoom_passcode',
        'url',
    ];

    // This ensures the Material knows which Lesson it belongs to
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'material_user')
            ->withPivot('watch_count')
            ->withTimestamps();
    }
}
