<x-filament-panels::page>

{{-- HOME / BACK TO DASHBOARD BUTTON --}}
    <div style="margin-bottom: 0.5rem;">
        <x-filament::button tag="a" href="/student" color="gray" icon="heroicon-m-home">
            Back to Dashboard
        </x-filament::button>
    </div>

    {{-- Responsive Grid: 1 column on mobile, 2 on tablets/mini-PCs, 3 on large monitors --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        
        @forelse ($this->lessons as $lesson)
            {{-- NATIVE FILAMENT SECTION: Guarantees dark mode borders, rounded corners, and card shadows --}}
            <x-filament::section class="flex flex-col justify-between h-full border border-gray-800 shadow-lg hover:border-primary-500/50 transition duration-200">
                
                <div>
                    {{-- FIELD 1: LESSON NAME & FIELD 4: AMOUNT --}}
                    <div class="flex items-start justify-between gap-3 border-b border-gray-800/80 pb-4 mb-4">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight leading-snug">
                            {{ $lesson->name }}
                        </h3>
                        
                        <x-filament::badge color="warning" size="lg" class="shrink-0 font-mono font-bold text-sm">
                            Rs. {{ number_format($lesson->fee ?? 0, 2) }}
                        </x-filament::badge>
                    </div>

                    {{-- FIELD 3: NEXT LIVE CLASS --}}
                    <div class="mb-4 bg-gray-950 dark:bg-gray-900/80 p-3 rounded-xl border border-gray-800/80 flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            <x-filament::icon icon="heroicon-o-calendar-days" class="w-4 h-4 text-primary-500" />
                            Next Live Class:
                        </span>
                        
                        @if ($lesson->next_class_at)
                            <span class="text-xs font-bold font-mono text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/20">
                                {{ \Carbon\Carbon::parse($lesson->next_class_at)->format('M d, Y @ h:i A') }}
                            </span>
                        @else
                            <span class="text-xs font-semibold text-gray-400 italic">Recorded Archive</span>
                        @endif
                    </div>

                    {{-- FIELD 2: DESCRIPTION --}}
                    @if (!empty($lesson->description))
                        <div class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed mb-6 line-clamp-3">
                            <span class="font-bold text-gray-400 block mb-1 uppercase tracking-wider text-[10px]">Description:</span>
                            {{ $lesson->description }}
                        </div>
                    @endif
                </div>

                {{-- ACTION BUTTON: Triggers the Bank Slip Uploader Modal --}}
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 mt-auto">
                    <x-filament::button 
                        color="primary" 
                        size="md"
                        class="w-full font-bold shadow-md" 
                        icon="heroicon-m-arrow-up-tray"
                        wire:click="mountAction('requestLesson', { lesson: {{ $lesson->id }} })"
                    >
                        Request Lesson & Upload Slip
                    </x-filament::button>
                </div>

            </x-filament::section>
        @empty
            
            <div class="col-span-full py-16 text-center bg-gray-900/40 border border-dashed border-gray-800 rounded-2xl p-8">
                <x-filament::icon icon="heroicon-o-shopping-bag" class="w-12 h-12 mx-auto text-gray-500 mb-3" />
                <h3 class="text-base font-bold text-gray-300">Catalog Empty</h3>
                <p class="text-xs text-gray-500 mt-1">There are no new lessons available for you to request at this time.</p>
            </div>

        @endforelse

    </div>

</x-filament-panels::page>