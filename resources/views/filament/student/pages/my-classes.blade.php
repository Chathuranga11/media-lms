<x-filament-panels::page>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 sm:gap-8">
        
        @forelse ($enrollments as $enrollment)
            @if ($enrollment->lesson)
                @php
                    $rawStatus = strtolower(trim($enrollment->status ?? ''));
                    $isPaid    = in_array($rawStatus, ['paid', 'active', 'approved']);
                    $isBasic   = in_array($rawStatus, ['free', 'postpay', 'postpaid']);
                    $isPending = !$isPaid && !$isBasic;

                    $materials = \Illuminate\Support\Facades\DB::table('materials')
                        ->where('lesson_id', $enrollment->lesson->id)
                        ->get();

                    $liveClasses = $materials->filter(fn($m) => strtolower($m->type) === 'live');
                    $pdfs        = $materials->filter(fn($m) => strtolower($m->type) === 'pdf');
                    $recordings  = $materials->filter(fn($m) => strtolower($m->type) === 'recording');
                @endphp

                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden flex flex-col h-full transition-all hover:shadow-md">
                    
                    {{-- 1. CARD HEADER (Solid Background) --}}
                    <div class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10 px-6 py-5 flex items-center justify-between gap-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white tracking-tight truncate">
                            {{ $enrollment->lesson->name }}
                        </h3>
                        <x-filament::badge 
                            color="{{ $isPaid ? 'success' : ($isBasic ? 'info' : 'warning') }}" 
                            size="sm" 
                            class="shrink-0 font-bold tracking-wider uppercase"
                        >
                            {{ $enrollment->status ?? 'Requested' }}
                        </x-filament::badge>
                    </div>

                    {{-- 2. CARD BODY --}}
                    <div class="flex-1 p-0">
                        @if ($isPaid || $isBasic)
                            
                            {{-- SEAMLESS LIST LAYOUT --}}
                            <ul class="divide-y divide-gray-100 dark:divide-white/10">

                                {{-- A. LIVE CLASSES --}}
                                @foreach ($liveClasses as $live)
                                    <li class="p-6 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                                            
                                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                                {{-- Circular Icon Badge --}}
                                                <div class="relative shrink-0 w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                                    <span class="absolute inset-0 rounded-full animate-ping bg-emerald-400 opacity-20"></span>
                                                    <x-filament::icon icon="heroicon-s-video-camera" class="w-6 h-6 text-emerald-600 dark:text-emerald-400 relative z-10" />
                                                </div>
                                                
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                                        {{ $live->title ?: 'Live Classroom' }}
                                                    </p>
                                                    @if (!empty($live->zoom_passcode) && $live->zoom_passcode !== 'N/A')
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                            Passcode: <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 select-all">{{ $live->zoom_passcode }}</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <x-filament::button tag="a" href="{{ $live->zoom_url }}" target="_blank" color="success" class="shrink-0 w-full sm:w-auto shadow-sm">
                                                Join Live
                                            </x-filament::button>
                                            
                                        </div>
                                    </li>
                                @endforeach

                                {{-- B. PDF RESOURCES --}}
                                @foreach ($pdfs as $pdf)
                                    <li class="p-6 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                                            
                                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                                <div class="shrink-0 w-12 h-12 rounded-full bg-sky-100 dark:bg-sky-500/20 flex items-center justify-center">
                                                    <x-filament::icon icon="heroicon-s-document-text" class="w-6 h-6 text-sky-600 dark:text-sky-400" />
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                                        {{ $pdf->title ?: 'Lesson Material' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">PDF Document</p>
                                                </div>
                                            </div>

                                            <x-filament::button tag="a" href="{{ route('student.download.material', ['id' => $pdf->id]) }}" target="_blank" color="info" variant="outlined" class="shrink-0 w-full sm:w-auto">
                                                Download Notes
                                            </x-filament::button>
                                            
                                        </div>
                                    </li>
                                @endforeach

                                {{-- C. RECORDINGS (Interactive) --}}
                                @if ($recordings->isNotEmpty())
                                    @if ($isPaid)
                                        @foreach ($recordings as $rec)
                                            @php
                                                $isUnlockedNow = !empty($unlockedVideos[$rec->id]);
                                                $materialRecord = auth()->user()->materials()->where('material_id', $rec->id)->first();
                                                $watchCount = $materialRecord ? $materialRecord->pivot->watch_count : 0;
                                                
                                                $maxViews = 3;
                                                $remainingViews = max(0, $maxViews - $watchCount);
                                                $progressPercentage = ($remainingViews / $maxViews) * 100;
                                                $isLocked = $remainingViews === 0;

                                                $cleanRecUrl = $rec->zoom_url ?? $rec->link ?? $rec->url ?? '';
                                            @endphp

                                            <li x-data="{ copied: false }" class="p-6 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                                
                                                <div class="flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-6">
                                                    
                                                    {{-- Icon & Info Column --}}
                                                    <div class="flex gap-4 flex-1 min-w-0">
                                                        <div class="shrink-0 w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                                                            <x-filament::icon icon="heroicon-s-play" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                                                        </div>
                                                        
                                                        <div class="min-w-0 flex-1 pt-1">
                                                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                                                {{ $rec->title ?: 'Cloud Recording' }}
                                                            </p>
                                                            
                                                            @if (!empty($rec->zoom_passcode) && $rec->zoom_passcode !== 'N/A')
                                                                <button @click="navigator.clipboard.writeText('{{ $rec->zoom_passcode }}'); copied = true; setTimeout(() => copied = false, 2000)" type="button" class="mt-1 text-xs font-mono font-medium text-amber-600 dark:text-amber-400 hover:text-amber-700 transition flex items-center gap-1 cursor-pointer">
                                                                    <x-filament::icon icon="heroicon-o-clipboard-document" class="w-3.5 h-3.5" />
                                                                    <span x-text="copied ? 'Passcode Copied!' : 'Passcode: {{ $rec->zoom_passcode }}'"></span>
                                                                </button>
                                                            @endif

                                                            {{-- Compact Progress Bar --}}
                                                            <div class="mt-3 max-w-xs">
                                                                <div class="flex justify-between items-center mb-1.5">
                                                                    <span class="text-[10px] uppercase font-bold tracking-wider text-gray-500">Views Left</span>
                                                                    <span class="text-[11px] font-bold {{ $isLocked ? 'text-red-500' : 'text-gray-700 dark:text-gray-300' }}">
                                                                        {{ $remainingViews }} / {{ $maxViews }}
                                                                    </span>
                                                                </div>
                                                                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                                                    <div class="h-full rounded-full transition-all duration-500 {{ $remainingViews > 1 ? 'bg-amber-500' : ($remainingViews == 1 ? 'bg-orange-500' : 'bg-red-500') }}" style="width: {{ $progressPercentage }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Action Button Column --}}
                                                    <div class="shrink-0 w-full sm:w-auto mt-4 sm:mt-1">
                                                        @if($isUnlockedNow)
                                                            <x-filament::button wire:click="$set('unlockedVideos.{{ $rec->id }}', false)" color="gray" class="w-full shadow-sm">
                                                                Close Player
                                                            </x-filament::button>
                                                        @elseif($isLocked)
                                                            <div class="inline-flex items-center justify-center px-4 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-lg text-sm font-bold w-full ring-1 ring-red-500/20">
                                                                <x-filament::icon icon="heroicon-m-lock-closed" class="w-4 h-4 mr-2" />
                                                                Locked
                                                            </div>
                                                        @else
                                                            <div class="relative w-full">
                                                                <x-filament::button wire:click="unlockVideo({{ $rec->id }})" color="warning" class="w-full shadow-sm">
                                                                    Load Replay
                                                                </x-filament::button>
                                                                <div wire:loading wire:target="unlockVideo({{ $rec->id }})" class="absolute inset-0 flex items-center justify-center bg-amber-500 rounded-lg">
                                                                    <x-filament::icon icon="heroicon-o-arrow-path" class="w-5 h-5 text-white animate-spin" />
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Inline Player Expansion --}}
                                                @if($isUnlockedNow)
                                                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/10 animate-fade-in">
                                                        <div class="relative w-full aspect-[16/10] sm:aspect-video bg-black rounded-xl overflow-hidden ring-1 ring-gray-950/10 shadow-2xl">
                                                            <iframe src="{{ $cleanRecUrl }}" class="absolute inset-0 w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
                                                        </div>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    @else
                                        {{-- RECORDINGS LOCKED FOR UNPAID --}}
                                        <li class="p-8 text-center bg-gray-50/50 dark:bg-white/[0.01]">
                                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 mb-3">
                                                <x-filament::icon icon="heroicon-m-lock-closed" class="w-6 h-6 text-gray-400" />
                                            </div>
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Recordings Locked</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">Cloud recordings are a premium feature. Please upgrade to a paid subscription to unlock them.</p>
                                        </li>
                                    @endif
                                @endif

                            </ul>
                        
                        @else
                            {{-- PENDING VERIFICATION STATE --}}
                            <div class="flex flex-col items-center justify-center p-12 text-center h-full min-h-[300px] bg-amber-50/30 dark:bg-amber-500/5">
                                <div class="w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mb-4">
                                    <x-filament::icon icon="heroicon-o-clock" class="w-8 h-8 text-amber-600 dark:text-amber-400 animate-pulse" />
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Verification Pending</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm leading-relaxed">
                                    We have received your payment slip. An administrator will verify your transaction shortly to unlock your classroom materials.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full flex flex-col items-center justify-center bg-white dark:bg-gray-900 rounded-2xl p-16 text-center shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 min-h-[400px]">
                <div class="w-20 h-20 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center mb-5">
                    <x-filament::icon icon="heroicon-o-academic-cap" class="w-10 h-10 text-gray-400" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Active Classes</h3>
                <p class="text-gray-500">You haven't enrolled in any learning modules yet.</p>
                <x-filament::button tag="a" href="/student/enroll" class="mt-6" color="primary">
                    Browse Courses
                </x-filament::button>
            </div>
        @endforelse

    </div>
</x-filament-panels::page>