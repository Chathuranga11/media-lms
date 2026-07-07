<x-filament-widgets::widget>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-top: 0.5rem;">

        {{-- 1. My Classes --}}
        <a href="/student/my-classes" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem; background-color: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 0.75rem; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; background-color: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                <x-filament::icon icon="heroicon-o-book-open" style="width: 1.75rem; height: 1.75rem; color: #f59e0b;" />
            </div>
            <span style="font-weight: bold; font-size: 0.95rem; color: inherit;">My Classes</span>
        </a>

        {{-- 2. Receipt Upload --}}
        <a href="/student/upcoming-catalog" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem; background-color: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 0.75rem; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; background-color: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                <x-filament::icon icon="heroicon-o-sparkles" style="width: 1.75rem; height: 1.75rem; color: #10b981;" />
            </div>
            <span style="font-weight: bold; font-size: 0.95rem; color: inherit; text-align: center;">Receipt Upload</span>
        </a>

        {{-- 3. Recordings --}}
        <a href="/student/archive-catalog" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem; background-color: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.2); border-radius: 0.75rem; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; background-color: rgba(14, 165, 233, 0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                <x-filament::icon icon="heroicon-o-archive-box" style="width: 1.75rem; height: 1.75rem; color: #0ea5e9;" />
            </div>
            <span style="font-weight: bold; font-size: 0.95rem; color: inherit;">Recordings</span>
        </a>

        {{-- 4. My Profile --}}
        <a href="/student/my-profile" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem; background-color: rgba(139, 92, 246, 0.05); border: 1px solid rgba(139, 92, 246, 0.2); border-radius: 0.75rem; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; background-color: rgba(139, 92, 246, 0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                <x-filament::icon icon="heroicon-o-user" style="width: 1.75rem; height: 1.75rem; color: #8b5cf6;" />
            </div>
            <span style="font-weight: bold; font-size: 0.95rem; color: inherit;">My Profile</span>
        </a>

    </div>
</x-filament-widgets::widget>