<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/join-zoom/{lesson}', [StudentController::class, 'joinZoom'])->name('student.join.zoom');

    // --- SECURE LIBRARIAN ROUTE (Now wired directly to the `materials` table) ---
    Route::get('/student/download-material/{id}', function ($id) {
        // Fetch material directly from DB to avoid model namespace mismatches
        $material = DB::table('materials')->where('id', $id)->first();
        if (!$material || empty($material->url)) abort(404, 'Material file reference not found.');

        // Verify enrollment ownership
        $isEnrolled = Enrollment::where('user_id', Auth::id())
            ->where('lesson_id', $material->lesson_id)
            ->exists();

        if (!$isEnrolled) abort(403, 'Access Denied: Active class enrollment required.');

        $cleanName = basename($material->url);
        $searchPaths = [
            storage_path('app/private/' . $material->url),
            storage_path('app/private/lesson-materials/' . $cleanName),
            storage_path('app/public/' . $material->url),
            storage_path('app/public/lesson-materials/' . $cleanName),
            storage_path('app/' . $material->url)
        ];

        foreach ($searchPaths as $path) {
            if (file_exists($path)) return response()->download($path, $cleanName);
        }

        abort(404, 'Physical file missing from server storage vault.');
    })->name('student.download.material');


    // --- URL INTERCEPTOR (Catches direct guesses to /storage/...pdf) ---
    $interceptor = function ($filename) {
        $cleanName = basename($filename);
        $material = DB::table('materials')->where('url', 'LIKE', '%' . $cleanName)->first();

        if ($material) {
            $enrolled = Enrollment::where('user_id', Auth::id())->where('lesson_id', $material->lesson_id)->exists();
            if (!$enrolled) abort(403, 'Access Denied.');
        }

        $paths = [
            storage_path('app/private/lesson-materials/' . $cleanName),
            storage_path('app/public/lesson-materials/' . $cleanName),
        ];

        foreach ($paths as $p) if (file_exists($p)) return response()->download($p);
        abort(404);
    };

    Route::get('/storage/lesson-materials/{filename}', $interceptor)->where('filename', '.*');
    Route::get('/lesson-materials/{filename}', $interceptor)->where('filename', '.*');
});
