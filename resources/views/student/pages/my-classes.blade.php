<x-filament-panels::page>

    {{-- THE GRID FIX: Shifted 3-columns to 'xl:' so Tablets & Mini-PCs get wide 2-column cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6">
        
        @forelse ($enrollments as $enrollment)
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 sm:p-6 shadow-sm flex flex-col justify-between hover:border-primary-500/50 transition-all duration-200">
                
                {{-- TOP ROW: Status Badge & Date --}}
                <div>
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wide bg-primary-50 text-primary-700 dark:bg-primary-950/80 dark:text-primary-300 ring-1 ring-inset ring-primary-500/20">
                            <x-filament::icon icon="heroicon-m-check-badge" class="w-3.5 h-3.5 text-primary-500" />
                            {{ ucfirst($enrollment->status ?? 'Enrolled') }}
                        </span>

                        <span class="text-xs font-medium text-gray-400 dark:text-gray-500 flex items-center gap-1">
                            <x-filament::icon icon="heroicon-m-calendar" class="w-3.5 h-3.5 shrink-0" />
                            {{ $enrollment->created_at->format('M d, Y') }}
                        </span>
                    </div>

                    {{-- LESSON TITLE --}}
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white tracking-tight mb-4 line-clamp-2">
                        {{ $enrollment->lesson->title ?? $enrollment->lesson->name ?? 'Class Lesson' }}
                    </h3>
                </div>

                {{-- CARD ACTIONS & MATERIALS --}}
                <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800/80">
                    
                    @if (in_array(strtolower($enrollment->status ?? ''), ['paid', 'free', 'postpay', 'postpaid']) && $enrollment->lesson)
                        
                        {{-- 1. COURSE TUTE / PDF --}}
                        @if (!empty($enrollment->lesson->pdf_url) || !empty($enrollment->lesson->pdf_file)) 
                            <div class="flex flex-col xs:flex-row xs:items-center justify-between gap-2.5 p-3 rounded-xl bg-sky-50 dark:bg-sky-950/30 border border-sky-100 dark:border-sky-900/40">
                                <div class="flex items-center gap-2 min-w-0 pr-2">
                                    <x-filament::icon icon="heroicon-o-document-text" class="w-4 h-4 text-sky-500 shrink-0" />
                                    <span class="text-xs font-bold text-sky-950 dark:text-sky-200 truncate">Course Tute / Notes</span>
                                </div>

                                <a 
                                    href="{{ route('student.download.pdf', ['lesson' => $enrollment->lesson->id]) }}" 
                                    target="_blank" 
                                    class="w-full xs:w-auto inline-flex items-center justify-center min-h-[40px] px-3.5 py-2 rounded-lg text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-sm transition-all active:scale-95 touch-manipulation select-none"
                                >
                                    Download PDF
                                </a>
                            </div>
                        @endif

                        {{-- 2. INLINE RECORDING PLAYER (Zero extra clicks required) --}}
                        @if (!empty($enrollment->lesson->recording_link) || !empty($enrollment->lesson->recording_url))
                            @php
                                $rawRec = $enrollment->lesson->recording_link ?? $enrollment->lesson->recording_url ?? '';
                                $cleanRecUrl = '';

                                if (preg_match('/src=["\']([^"\']+)["\']/i', $rawRec, $m)) {
                                    $cleanRecUrl = $m[1];
                                } elseif (preg_match('/https?:\/\/[^\s"\'<>]+/i', $rawRec, $m)) {
                                    $cleanRecUrl = $m[0];
                                }

                                $recPw = $enrollment->lesson->recording_passcode ?? '';
                                $hasRecPw = !empty($recPw) && $recPw !== 'N/A';
                            @endphp

                            @if (!empty($cleanRecUrl))
                                <div class="space-y-2 pt-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                            <x-filament::icon icon="heroicon-o-video-camera" class="w-4 h-4 text-amber-500 shrink-0" />
                                            Class Recording
                                        </span>

                                        @if ($hasRecPw)
                                            <div x-data="{ copied: false }">
                                                <button 
                                                    @click="navigator.clipboard.writeText('{{ $recPw }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                                    type="button" 
                                                    class="font-mono bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-amber-600 dark:text-amber-400 px-2 py-0.5 rounded transition-all cursor-pointer active:scale-95 text-[11px]"
                                                    title="Click to copy passcode"
                                                >
                                                    <span x-text="copied ? 'Copied!' : 'PW: {{ $recPw }}'"></span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- THE UNIVERSAL RESPONSIVE VIDEO BOX --}}
                                    {{-- 'inset-0 w-full h-full' forces the iframe to physically obey the parent aspect ratio --}}
                                    <div class="relative w-full aspect-[16/10] min-h-[210px] rounded-xl overflow-hidden bg-black border border-gray-800 shadow-inner">
                                        <iframe 
                                            src="{{ $cleanRecUrl }}" 
                                            class="absolute inset-0 w-full h-full border-0 bg-black" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen
                                            loading="lazy"
                                        ></iframe>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- 3. LIVE ZOOM SESSION --}}
                        @if (!empty($enrollment->lesson->live_class_link) || !empty($enrollment->lesson->zoom_link))
                            @php
                                $livePw = $enrollment->lesson->live_class_passcode ?? $enrollment->lesson->zoom_passcode ?? '';
                                $hasLivePw = !empty($livePw) && $livePw !== 'N/A';
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 pt-2.5">
                                <div class="flex items-center gap-2 truncate pr-2">
                                    <span class="relative flex h-2.5 w-2.5 shrink-0">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-xs font-bold text-emerald-950 dark:text-emerald-200 truncate">Live Class Active</span>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if ($hasLivePw)
                                        <div x-data="{ copied: false }">
                                            <button 
                                                @click="navigator.clipboard.writeText('{{ $livePw }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                                type="button" 
                                                class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-emerald-500/20 text-emerald-900 dark:text-emerald-300 transition-all active:scale-95 select-none"
                                            >
                                                <span x-text="copied ? 'Copied!' : 'PW: {{ $livePw }}'"></span>
                                            </button>
                                        </div>
                                    @endif

                                    <a 
                                        href="{{ route('student.join.zoom', ['lesson' => $enrollment->lesson->id]) }}" 
                                        target="_blank" 
                                        class="min-h-[36px] inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-all active:scale-95 touch-manipulation select-none"
                                    >
                                        Join Live
                                    </a>
                                </div>
                            </div>
                        @endif

                    @else
                        {{-- LOCKED STATE --}}
                        <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-center">
                            <p class="text-xs font-bold text-amber-600 dark:text-amber-400">Approval Pending</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Course recordings and materials unlock automatically once an admin confirms your payment receipt.</p>
                        </div>
                    @endif

                </div>

            </div>
            
        @empty
            
            <div class="col-span-full bg-white dark:bg-gray-900 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-800">
                <p class="text-gray-500 dark:text-gray-400 text-sm">You have not registered for any active classes yet.</p>
            </div>
            
        @endforelse

    </div>

</x-filament-panels::page>