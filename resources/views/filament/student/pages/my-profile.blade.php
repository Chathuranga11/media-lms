<x-filament-panels::page>
    
    {{-- Reverted back to the standard HTML form tag --}}
    <form wire:submit="save">
        
        {{-- Renders the beautiful form schema we built in the PHP file --}}
        {{ $this->form }}

        {{-- Spacing and Filament's native button --}}
        <div class="mt-6 text-left">
            <x-filament::button type="submit" size="lg" color="primary">
                Save Changes
            </x-filament::button>
        </div>
        
    </form>
    
</x-filament-panels::page>