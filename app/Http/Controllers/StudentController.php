<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Securely serve the PDF document based on enrollment status.
     */
    public function downloadPdf(Lesson $lesson)
    {
        // Fix 1: Use Auth::id() instead of auth()->id()
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->first();

        // 1. Check if enrolled AND if the status allows access
        if (!$enrollment || !$enrollment->canAccessBasicMaterials()) {
            abort(403, 'You do not have permission to access these materials. Please verify your payment status.');
        }

        // 2. Prevent a server crash if the admin forgot to upload the PDF
        if (!$lesson->pdf_path || !Storage::disk('public')->exists($lesson->pdf_path)) {
            abort(404, 'The PDF document is not available at the moment.');
        }

        // Fix 2: Use response()->download() which the editor understands perfectly
        return response()->download(storage_path('app/public/' . $lesson->pdf_path));
    }

    /**
     * Securely redirect to the live Zoom class.
     */
    public function joinZoom(Lesson $lesson)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->first();

        if (!$enrollment || !$enrollment->canAccessBasicMaterials()) {
            abort(403, 'Your payment must be verified to join the live class.');
        }

        if (!$lesson->zoom_link) {
            abort(404, 'The Zoom link for this class has not been set yet.');
        }

        // Redirect the student directly to the Zoom application/website
        return redirect()->away($lesson->zoom_link);
    }
    public function dashboard()
    {
        // Fetch all enrollments for the currently logged-in student, including the related lesson data
        $enrollments = \App\Models\Enrollment::where('user_id', Auth::id())
            ->with('lesson')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.dashboard', compact('enrollments'));
    }
}
