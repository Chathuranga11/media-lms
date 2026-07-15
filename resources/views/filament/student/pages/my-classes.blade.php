<x-filament-panels::page>
    
    {{-- HOME / BACK TO DASHBOARD BUTTON --}}
    <div style="margin-bottom: 0.5rem;">
        <x-filament::button tag="a" href="/student" color="gray" icon="heroicon-m-home">
            Back to Dashboard
        </x-filament::button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem;">
        
        @forelse ($enrollments as $enrollment)
            @if ($enrollment->lesson)
                @php
                    $rawStatus  = strtolower(trim($enrollment->status ?? ''));
                    
                    // Define student access tiers
                    $isPaidHall = $rawStatus === 'paid_hall';
                    $isPaid     = in_array($rawStatus, ['paid', 'active', 'approved']);
                    $isBasic    = in_array($rawStatus, ['free', 'postpay', 'postpaid']);
                    $hasAccess  = $isPaidHall || $isPaid || $isBasic;

                    // 1. Get ALL materials for this lesson
                    $allMaterials = \Illuminate\Support\Facades\DB::table('materials')
                                        ->where('lesson_id', $enrollment->lesson->id)
                                        ->get();

                    // 2. ENFORCE AUDIENCE LOGIC: Filter materials based on the student's tier
                    $materials = $allMaterials->filter(function($m) use ($isPaidHall, $isPaid, $isBasic) {
                        $audience = strtolower(trim($m->audience ?? 'all'));

                        if ($audience === 'all') {
                            return true; 
                        }
                        if ($audience === 'paid_hall' && $isPaidHall) {
                            return true; 
                        }
                        if ($audience === 'paid' && ($isPaid || $isBasic)) {
                            return true; 
                        }
                        return false; 
                    });

                    // 3. Categorize the filtered materials
                    $liveClasses = $materials->filter(fn($m) => strtolower($m->type) === 'live');
                    $pdfs        = $materials->filter(fn($m) => strtolower($m->type) === 'pdf');
                    $recordings  = $materials->filter(fn($m) => strtolower($m->type) === 'recording');
                @endphp

                <x-filament::section>
                    
                    {{-- 1. CARD HEADER --}}
                    <x-slot name="heading">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;">
                            {{-- REMOVED ELLIPSIS, ALLOWED WRAPPING --}}
                            <span style="font-size: 1.15rem; font-weight: bold; word-break: break-word; line-height: 1.3;">
                                {{ $enrollment->lesson->name }}
                            </span>
                            <x-filament::badge color="{{ $hasAccess ? 'success' : 'warning' }}" size="sm" style="flex-shrink: 0; text-transform: uppercase; font-weight: bold; margin-top: 0.15rem;">
                                {{ $enrollment->status ?? 'Requested' }}
                            </x-filament::badge>
                        </div>
                    </x-slot>

                    {{-- 2. CARD BODY --}}
                    <div style="display: flex; flex-direction: column; padding-top: 0.5rem;">
                        @if ($hasAccess)

                            {{-- A. LIVE CLASSES --}}
                            @foreach ($liveClasses as $live)
                                <div wire:key="live-{{ $live->id }}" style="display: flex; flex-wrap: wrap; align-items: center; gap: 1.5rem; padding: 1.5rem 0; border-bottom: 1px solid rgba(156, 163, 175, 0.2);">
                                    <div style="display: flex; align-items: flex-start; gap: 1rem; flex: 1; min-width: 0;">
                                        <div style="width: 3rem; height: 3rem; border-radius: 50%; background-color: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <x-filament::icon icon="heroicon-s-video-camera" style="width: 1.5rem; height: 1.5rem;" />
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            {{-- REMOVED ELLIPSIS, ALLOWED WRAPPING --}}
                                            <div style="font-weight: bold; font-size: 0.95rem; word-break: break-word; line-height: 1.3;">
                                                {{ $live->title ?: 'Live Classroom' }}
                                            </div>
                                            @if (!empty($live->zoom_passcode) && $live->zoom_passcode !== 'N/A')
                                                <div style="font-size: 0.75rem; font-family: monospace; opacity: 0.8; margin-top: 0.4rem;">
                                                    Passcode: <span style="color: #10b981; font-weight: bold; user-select: all;">{{ $live->zoom_passcode }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div style="flex-shrink: 0;">
                                        <x-filament::button tag="a" href="{{ $live->zoom_url }}" target="_blank" color="success">Join Live</x-filament::button>
                                    </div>
                                </div>
                            @endforeach

                            {{-- B. GROUPED PDFs --}}
                            @if ($pdfs->isNotEmpty())
                                <div style="padding: 1.5rem 0; border-bottom: 1px solid rgba(156, 163, 175, 0.2);">
                                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                                        <div style="width: 3rem; height: 3rem; border-radius: 50%; background-color: rgba(14, 165, 233, 0.15); color: #0ea5e9; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <x-filament::icon icon="heroicon-s-document-duplicate" style="width: 1.5rem; height: 1.5rem;" />
                                        </div>
                                        <div style="font-weight: bold; font-size: 1.05rem; color: inherit;">
                                            Course Notes & PDFs <span style="opacity: 0.6; font-size: 0.85rem; margin-left: 0.25rem;">({{ $pdfs->count() }})</span>
                                        </div>
                                    </div>

                                    <div style="display: flex; flex-direction: column; gap: 0.75rem; padding-left: 1rem; border-left: 2px solid rgba(14, 165, 233, 0.3); margin-left: 1.5rem;">
                                        @foreach ($pdfs as $pdf)
                                            <div wire:key="pdf-{{ $pdf->id }}" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.75rem 1rem; background: rgba(14, 165, 233, 0.05); border-radius: 0.5rem; border: 1px solid rgba(14, 165, 233, 0.1);">
                                                <div style="flex: 1; min-width: 0; display: flex; align-items: flex-start; gap: 0.75rem;">
                                                    <x-filament::icon icon="heroicon-m-document-text" style="width: 1.2rem; height: 1.2rem; color: #0ea5e9; flex-shrink: 0; margin-top: 0.1rem;" />
                                                    {{-- REMOVED ELLIPSIS, ALLOWED WRAPPING --}}
                                                    <div style="font-weight: 600; font-size: 0.85rem; word-break: break-word; line-height: 1.4;">
                                                        {{ $pdf->title ?: 'Lesson Material' }}
                                                    </div>
                                                </div>
                                                <x-filament::button tag="a" href="{{ route('student.download.material', ['id' => $pdf->id]) }}" target="_blank" color="info" size="xs" style="flex-shrink: 0;">
                                                    Download
                                                </x-filament::button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- C. GROUPED RECORDINGS --}}
                            @if ($recordings->isNotEmpty())
                                @if ($isPaid || $isPaidHall) 
                                    <div style="padding: 1.5rem 0; border-bottom: none;">
                                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                                            <div style="width: 3rem; height: 3rem; border-radius: 50%; background-color: rgba(245, 158, 11, 0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <x-filament::icon icon="heroicon-s-film" style="width: 1.5rem; height: 1.5rem;" />
                                            </div>
                                            <div style="font-weight: bold; font-size: 1.05rem; color: inherit;">
                                                Cloud Recordings <span style="opacity: 0.6; font-size: 0.85rem; margin-left: 0.25rem;">({{ $recordings->count() }})</span>
                                            </div>
                                        </div>

                                        <div style="display: flex; flex-direction: column; gap: 1rem; padding-left: 1rem; border-left: 2px solid rgba(245, 158, 11, 0.3); margin-left: 1.5rem;">
                                            @foreach ($recordings as $rec)
                                                @php
                                                    $isUnlockedNow = !empty($unlockedVideos[$rec->id]);
                                                    $materialRecord = auth()->user()->materials()->where('material_id', $rec->id)->first();
                                                    $watchCount = $materialRecord ? $materialRecord->pivot->watch_count : 0;
                                                    
                                                    $maxViews = 3;
                                                    $remainingViews = max(0, $maxViews - $watchCount);
                                                    $progressPercentage = ($remainingViews / $maxViews) * 100;
                                                    $isLocked = $remainingViews === 0;

                                                    $rawRec = $rec->zoom_url ?? $rec->link ?? $rec->url ?? '';
                                                    $cleanRecUrl = preg_match('/src=["\']([^"\']+)["\']/i', $rawRec, $m) ? $m[1] : (preg_match('/https?:\/\/[^\s"\'<>]+/i', $rawRec, $m) ? $m[0] : '');
                                                @endphp

                                                <div wire:key="rec-{{ $rec->id }}" x-data="{ copied: false }" style="background: rgba(245, 158, 11, 0.03); border: 1px solid rgba(245, 158, 11, 0.15); border-radius: 0.75rem; padding: 1.25rem;">
                                                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                                        
                                                        <div style="flex: 1; min-width: 0;">
                                                            <div style="display: flex; align-items: flex-start; gap: 0.5rem; font-weight: bold; font-size: 0.9rem;">
                                                                <x-filament::icon icon="heroicon-s-play-circle" style="width: 1.2rem; height: 1.2rem; color: #f59e0b; flex-shrink: 0; margin-top: 0.1rem;" />
                                                                {{-- REMOVED ELLIPSIS, ALLOWED WRAPPING --}}
                                                                <span style="word-break: break-word; line-height: 1.4;">{{ $rec->title ?: 'Cloud Replay' }}</span>
                                                            </div>
                                                            
                                                            @if (!empty($rec->zoom_passcode) && $rec->zoom_passcode !== 'N/A')
                                                                <button @click="navigator.clipboard.writeText('{{ $rec->zoom_passcode }}'); copied = true; setTimeout(() => copied = false, 2000)" type="button" style="margin-top: 0.75rem; font-size: 0.7rem; font-family: monospace; font-weight: bold; color: #f59e0b; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); padding: 0.2rem 0.5rem; border-radius: 0.25rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                                    <x-filament::icon icon="heroicon-o-clipboard-document" style="width: 0.8rem; height: 0.8rem;" />
                                                                    <span x-text="copied ? 'Copied!' : 'PW: {{ $rec->zoom_passcode }}'"></span>
                                                                </button>
                                                            @endif

                                                            {{-- Compact Progress Bar --}}
                                                            <div style="margin-top: 1rem; max-width: 16rem;">
                                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                                                                    <span style="font-size: 0.65rem; text-transform: uppercase; font-weight: bold; opacity: 0.6;">Views Left</span>
                                                                    <span style="font-size: 0.7rem; font-weight: bold; color: {{ $isLocked ? '#ef4444' : 'inherit' }};">
                                                                        {{ $remainingViews }} / {{ $maxViews }}
                                                                    </span>
                                                                </div>
                                                                <div style="width: 100%; height: 5px; background-color: rgba(156, 163, 175, 0.2); border-radius: 999px; overflow: hidden;">
                                                                    <div style="height: 100%; width: {{ $progressPercentage }}%; background-color: {{ $remainingViews > 1 ? '#f59e0b' : ($remainingViews == 1 ? '#ea580c' : '#ef4444') }}; transition: width 0.5s ease;"></div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- ACTION BUTTON COLUMN --}}
                                                        <div style="flex-shrink: 0; min-width: 130px; margin-top: 0.25rem;">
                                                            @if($isUnlockedNow)
                                                                <button type="button" wire:click.prevent="$set('unlockedVideos.{{ $rec->id }}', false)" style="width: 100%; background-color: rgba(156, 163, 175, 0.1); color: inherit; border: 1px solid rgba(156, 163, 175, 0.3); padding: 0.4rem 0.8rem; border-radius: 0.5rem; font-size: 0.85rem; font-weight: bold; cursor: pointer; transition: all 0.2s;">
                                                                    Close Player
                                                                </button>
                                                            @elseif($isLocked)
                                                                <div style="display: inline-flex; align-items: center; justify-content: center; padding: 0.4rem 0.8rem; background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; border-radius: 0.5rem; font-size: 0.85rem; font-weight: bold; width: 100%;">
                                                                    <x-filament::icon icon="heroicon-m-lock-closed" style="width: 1.1rem; height: 1.1rem; margin-right: 0.4rem;" />
                                                                    Locked
                                                                </div>
                                                            @else
                                                                <button type="button" 
                                                                    wire:click.prevent="unlockVideo({{ $rec->id }})" 
                                                                    wire:loading.attr="disabled"
                                                                    style="width: 100%; background-color: #f59e0b; color: white; border: none; padding: 0.5rem 0.8rem; border-radius: 0.5rem; font-size: 0.85rem; font-weight: bold; cursor: pointer; display: flex; justify-content: center; align-items: center; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                                                                >
                                                                    <span wire:loading.remove wire:target="unlockVideo({{ $rec->id }})">Load Replay</span>
                                                                    <span wire:loading wire:target="unlockVideo({{ $rec->id }})">Loading...</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                        
                                                    </div>

                                                    {{-- Inline Player Expansion --}}
                                                    @if($isUnlockedNow)
                                                        <div wire:key="player-{{ $rec->id }}" style="width: 100%; margin-top: 1rem; border-top: 1px solid rgba(156, 163, 175, 0.1); padding-top: 1.25rem;">
                                                            <div style="position: relative; width: 100%; padding-bottom: 56.25%; background-color: #000; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);">
                                                                <iframe src="{{ $cleanRecUrl }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen></iframe>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div style="padding: 2.5rem 1rem; text-align: center; opacity: 0.7;">
                                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; border-radius: 50%; background-color: rgba(156, 163, 175, 0.15); margin-bottom: 0.75rem;">
                                            <x-filament::icon icon="heroicon-m-lock-closed" style="width: 1.5rem; height: 1.5rem; color: #9ca3af;" />
                                        </div>
                                        <div style="font-weight: bold; font-size: 0.95rem; margin-bottom: 0.25rem;">Recordings Locked</div>
                                        <div style="font-size: 0.75rem; color: #9ca3af; max-width: 20rem; margin: 0 auto;">Cloud recordings are a premium feature. Please upgrade to a paid subscription to unlock them.</div>
                                    </div>
                                @endif
                            @endif
                        
                        @else
                            {{-- PENDING VERIFICATION STATE --}}
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 1rem; text-align: center; background-color: rgba(245, 158, 11, 0.05); border-radius: 0.5rem;">
                                <div style="width: 4rem; height: 4rem; border-radius: 50%; background-color: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                    <x-filament::icon icon="heroicon-o-clock" style="width: 2rem; height: 2rem; color: #f59e0b;" />
                                </div>
                                <h4 style="font-size: 1.15rem; font-weight: bold; color: #f59e0b; margin-bottom: 0.5rem;">Verification Pending</h4>
                                <p style="font-size: 0.85rem; opacity: 0.8; max-width: 24rem; line-height: 1.5;">
                                    We have received your payment slip. An administrator will verify your transaction shortly to unlock your classroom materials.
                                </p>
                            </div>
                        @endif
                    </div>
                </x-filament::section>
            @endif
        @empty
            <div style="grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4rem 2rem; border-radius: 1rem; border: 1px solid rgba(156, 163, 175, 0.2); text-align: center;">
                <x-filament::icon icon="heroicon-o-academic-cap" style="width: 3.5rem; height: 3.5rem; color: #9ca3af; margin-bottom: 1rem;" />
                <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">No Active Classes</h3>
                <p style="font-size: 0.9rem; color: #9ca3af; margin-bottom: 1.5rem;">You haven't enrolled in any learning modules yet.</p>
                <x-filament::button tag="a" href="/student/enroll" color="primary">Browse Courses</x-filament::button>
            </div>
        @endforelse

    </div>
</x-filament-panels::page>