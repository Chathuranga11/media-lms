<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($this->getFlyers() as $flyer)
                <div class="rounded-xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 transition-transform duration-300 hover:scale-105">
                    <img src="{{ asset('storage/' . $flyer->image) }}" alt="{{ $flyer->title }}" class="w-full h-auto object-cover">
                </div>
            @empty
                <div class="col-span-full text-center text-slate-500 py-6">
                    No announcements at the moment. Check back soon!
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>