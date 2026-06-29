<div class="lesson-dashboard">
    <h2>{{ $enrollment->lesson->title }}</h2>

    {{-- 1. PENDING PAYMENT STATE --}}
    @if($enrollment->isPendingPayment())
        <div class="alert alert-warning">
            <p>Your enrollment is currently <strong>{{ ucfirst($enrollment->status) }}</strong>.</p>
            <p>Please upload your bank slip or wait for admin approval to unlock this class.</p>
            <a href="/student/upload-slip/{{ $enrollment->id }}" class="btn btn-primary">Upload Bank Slip</a>
        </div>
    @endif

    {{-- 2. BASIC ACCESS (Live Class & PDFs) --}}
    @if($enrollment->canAccessBasicMaterials())
        <div class="materials-section">
            <h3>Class Materials</h3>
            <a href="{{ $enrollment->lesson->zoom_link }}" class="btn btn-success">Join Live Zoom Class</a>
            <a href="/student/download-pdf/{{ $enrollment->lesson->id }}" class="btn btn-info">Download PDF Notes</a>
        </div>
    @endif

    {{-- 3. PREMIUM ACCESS (Recordings) --}}
    @if($enrollment->canAccessRecordings())
        <div class="recordings-section">
            <h3>Cloud Recordings</h3>
            <a href="{{ $enrollment->lesson->recording_url }}" class="btn btn-dark">Watch Replay</a>
        </div>
    @elseif($enrollment->canAccessBasicMaterials())
        {{-- Teaser for Free/Postpay students --}}
        <div class="alert alert-secondary">
            <p>Cloud recordings are available for fully Paid students. Upgrade to unlock replays!</p>
        </div>
    @endif
</div>