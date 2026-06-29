<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // <-- Added to talk to the materials table

class Lesson extends Model
{
    // Keeping $guarded empty allows Filament to save 100% of your form fields safely
    protected $guarded = [];

    // A lesson has many enrolled students
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // A lesson has many zoom recordings and PDFs
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    // --- THE AUTOMAGIC FILAMENT BRIDGE ---
    // Every time Filament creates or updates a lesson, this wakes up and stocks the materials table
    protected static function booted(): void
    {
        static::saved(function (Lesson $lesson) {

            // 1. PIPE PDF UPLOADS $\rightarrow$ 'materials' table
            if (!empty($lesson->pdf_material_path)) {
                $pdfExists = DB::table('materials')
                    ->where('lesson_id', $lesson->id)
                    ->where('type', 'pdf')
                    ->exists();

                if ($pdfExists) {
                    DB::table('materials')
                        ->where('lesson_id', $lesson->id)
                        ->where('type', 'pdf')
                        ->update([
                            'title' => $lesson->name . ' - Course PDF',
                            'url' => $lesson->pdf_material_path,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('materials')->insert([
                        'lesson_id' => $lesson->id,
                        'type' => 'pdf',
                        'title' => $lesson->name . ' - Course PDF',
                        'url' => $lesson->pdf_material_path,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 2. PIPE ZOOM LINKS $\rightarrow$ 'materials' table
            if (!empty($lesson->zoom_link)) {
                $liveExists = DB::table('materials')
                    ->where('lesson_id', $lesson->id)
                    ->where('type', 'live')
                    ->exists();

                if ($liveExists) {
                    DB::table('materials')
                        ->where('lesson_id', $lesson->id)
                        ->where('type', 'live')
                        ->update([
                            'title' => $lesson->name . ' - Live Classroom',
                            'zoom_url' => $lesson->zoom_link,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('materials')->insert([
                        'lesson_id' => $lesson->id,
                        'type' => 'live',
                        'title' => $lesson->name . ' - Live Classroom',
                        'zoom_url' => $lesson->zoom_link,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }
}
