<x-filament-panels::page>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        
        @forelse ($enrollments as $enrollment)
            @if ($enrollment->lesson)
                @php
                    // 1. NORMALIZE THE STATUS (Strips accidental spaces & ignores capital letters)
                    $rawStatus = strtolower(trim($enrollment->status ?? ''));

                    $isPaid    = in_array($rawStatus, ['paid', 'active', 'approved']);
                    $isBasic   = in_array($rawStatus, ['free', 'postpay', 'postpaid']);
                    $isPending = !$isPaid && !$isBasic; // Catches 'Requested', 'Pending Verification', etc.

                    // 2. FETCH MATERIALS
                    $materials = \Illuminate\Support\Facades\DB::table('materials')
                        ->where('lesson_id', $enrollment->lesson->id)
                        ->get();

                    $liveClasses = $materials->filter(fn($m) => strtolower($m->type) === 'live');
                    $pdfs        = $materials->filter(fn($m) => strtolower($m->type) === 'pdf');
                    $recordings  = $materials->filter(fn($m) => strtolower($m->type) === 'recording');
                @endphp

                <x-filament::section class="flex flex-col justify-between h-full border border-gray-800 shadow-lg hover:border-gray-700 transition duration-200">
                    
                    {{-- CARD HEADER --}}
                    <x-slot name="heading">
                        <div class="flex items-center justify-between gap-3 border-b border-gray-800/80 pb-3">
                            <span class="text-xl font-black tracking-tight text-gray-900 dark:text-white truncate">
                                {{ $enrollment->lesson->name }}
                            </span>
                            
                            {{-- Dynamic Color Badge based on Tier --}}
                            <x-filament::badge 
                                color="{{ $isPaid ? 'success' : ($isBasic ? 'info' : 'warning') }}" 
                                class="shrink-0 font-mono"
                            >
                                {{ ucfirst($enrollment->status ?? 'Requested') }}
                            </x-filament::badge>
                        </div>
                    </x-slot>

                    <div class="space-y-6 pt-2">

                        {{-- ========================================================= --}}
                        {{-- GATEWAY A: APPROVED ACCESS (Paid + Free + Postpay)        --}}
                        {{-- ========================================================= --}}
                        @if ($isPaid || $isBasic)

                            {{-- 1. LIVE CLASSROOM --}}
                            @if ($liveClasses->isNotEmpty())
                                <div class="space-y-2">
                                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-500 flex items-center gap-1.5">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                        Live Classroom
                                    </div>

                                    @foreach ($liveClasses as $live)
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-gray-900 dark:text-emerald-200 truncate">
                                                    {{ $live->title ?: 'Scheduled Live Session' }}
                                                </p>
                                                @if (!empty($live->zoom_passcode) && $live->zoom_passcode !== 'N/A')
                                                    <p class="text-xs font-mono text-emerald-400 mt-0.5 select-all">
                                                        Passcode: {{ $live->zoom_passcode }}
                                                    </p>
                                                @endif
                                            </div>

                                            <x-filament::button tag="a" href="{{ $live->zoom_url }}" target="_blank" color="success" icon="heroicon-m-video-camera" class="shrink-0 w-full sm:w-auto font-bold">
                                                Join Live Class
                                            </x-filament::button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- 2. LESSON PDFs --}}
                            @if ($pdfs->isNotEmpty())
                                <div class="space-y-2">
                                    <div class="text-xs font-bold uppercase tracking-wider text-sky-400 flex items-center gap-1.5">
                                        <x-filament::icon icon="heroicon-o-folder-arrow-down" class="w-4 h-4" />
                                        Lesson PDFs & Tutes
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        @foreach ($pdfs as $pdf)
                                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-900/60 border border-gray-800 hover:border-sky-500/40 transition">
                                                <div class="flex items-center gap-2 min-w-0 pr-2">
                                                    <x-filament::icon icon="heroicon-m-document-text" class="w-4 h-4 text-sky-400 shrink-0" />
                                                    <span class="text-xs font-medium text-gray-300 truncate">
                                                        {{ $pdf->title ?: 'Course Material' }}
                                                    </span>
                                                </div>

                                                <x-filament::button tag="a" href="{{ route('student.download.material', ['id' => $pdf->id]) }}" target="_blank" size="xs" color="info" icon="heroicon-m-arrow-down-tray" class="shrink-0">
                                                    PDF
                                                </x-filament::button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- 3. RECORDINGS (Strictly locked to PAID tier only!) --}}
                            @if ($recordings->isNotEmpty())
                                <div class="space-y-2 pt-1">
                                    <div class="text-xs font-bold uppercase tracking-wider text-amber-500 flex items-center gap-1.5">
                                        <x-filament::icon icon="heroicon-o-film" class="w-4 h-4" />
                                        Lesson Recordings
                                    </div>

                                    @if ($isPaid)
                                        {{-- PAID USER: Show Click-to-Play VODs --}}
                                        <div class="space-y-3">
                                            @foreach ($recordings as $rec)
                                                <div x-data="{ showVideo: false, copied: false }" class="p-3.5 rounded-xl bg-gray-900/80 border border-gray-800 space-y-3">
                                                    
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="text-xs font-bold text-gray-200 truncate pr-2">
                                                            {{ $rec->title ?: 'Recorded Class Replay' }}
                                                        </span>

                                                        @if (!empty($rec->zoom_passcode) && $rec->zoom_passcode !== 'N/A')
                                                            <button 
                                                                @click="navigator.clipboard.writeText('{{ $rec->zoom_passcode }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                                                type="button" 
                                                                class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 transition shrink-0 cursor-pointer"
                                                            >
                                                                <span x-text="copied ? 'Copied!' : 'PW: {{ $rec->zoom_passcode }}'"></span>
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <div x-show="!showVideo">
                                                        <x-filament::button type="button" size="sm" color="warning" class="w-full font-bold" icon="heroicon-m-play" @click="showVideo = true">
                                                            Load Recording
                                                        </x-filament::button>
                                                    </div>

                                                    <template x-if="showVideo">
                                                        <div class="space-y-2.5 pt-1">
                                                            <div class="relative w-full aspect-[16/10] sm:aspect-video bg-black rounded-lg overflow-hidden border border-gray-800 shadow-2xl">
                                                                <iframe src="{{ $rec->zoom_url }}" class="absolute inset-0 w-full h-full border-0 bg-black" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
                                                            </div>
                                                            <x-filament::button type="button" size="xs" color="gray" class="w-full" icon="heroicon-m-chevron-up" @click="showVideo = false">Hide Player</x-filament::button>
                                                        </div>
                                                    </template>

                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        {{-- FREE / POSTPAY USER: Show locked upgrade banner --}}
                                        <div class="p-4 bg-gray-950/60 rounded-xl border border-gray-800/80 text-center space-y-1">
                                            <x-filament::icon icon="heroicon-m-lock-closed" class="w-5 h-5 mx-auto text-amber-500/80" />
                                            <p class="text-xs font-bold text-gray-300">Recordings Locked</p>
                                            <p class="text-[11px] text-gray-500 max-w-xs mx-auto">Cloud recording replays are available exclusively for verified Paid subscriptions.</p>
                                        </div>
                                    @endif

                                </div>
                            @endif


                        {{-- ========================================================= --}}
                        {{-- GATEWAY B: PENDING VERIFICATION / REQUESTED (Show Nothing)--}}
                        {{-- ========================================================= --}}
                        @else

                            <div class="py-10 px-4 text-center bg-gray-950/40 rounded-xl border border-amber-500/20 space-y-2">
                                <x-filament::icon icon="heroicon-o-clock" class="w-8 h-8 mx-auto text-amber-500 animate-pulse" />
                                <h4 class="text-sm font-bold text-amber-400">Verification in Progress</h4>
                                <p class="text-xs text-gray-400 max-w-xs mx-auto leading-relaxed">
                                    Your bank transfer receipt has been received and is waiting for admin confirmation. Your classroom will unlock automatically once approved.
                                </p>
                            </div>

                        @endif

                    </div>

                </x-filament::section>
            @endif
        @empty
            
            <div class="col-span-full bg-gray-900 rounded-2xl p-12 text-center border border-gray-800 shadow-sm">
                <x-filament::icon icon="heroicon-o-academic-cap" class="w-10 h-10 mx-auto text-gray-500 mb-2" />
                <h3 class="text-white font-bold text-base">No Enrolled Classes</h3>
                <p class="text-gray-400 text-xs mt-1">You haven't registered for any learning modules yet.</p>
            </div>

        @endforelse

    </div>

</x-filament-panels::page>