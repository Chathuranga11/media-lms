<x-filament-panels::page>
    <form wire:submit="createClass">
        {{ $this->form }}

        <div style="margin-top: 1.5rem;">
            <x-filament::button type="submit" size="lg">
                Generate Zoom Class
            </x-filament::button>
        </div>
    </form>

    {{-- SUCCESS RESULTS CARD --}}
    @if ($meetingDetails)
        <x-filament::section style="margin-top: 2rem; border-color: #10b981; background-color: rgba(16, 185, 129, 0.05);">
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #10b981;">
                    <x-filament::icon icon="heroicon-o-check-circle" style="width: 1.5rem; height: 1.5rem;" />
                    Zoom Meeting Successfully Generated!
                </div>
            </x-slot>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div>
                    <span style="font-weight: bold; font-size: 0.85rem; color: gray; text-transform: uppercase;">Class Title</span>
                    <div style="font-size: 1.1rem; font-weight: bold;">{{ $meetingDetails['title'] }}</div>
                </div>

                <div>
                    <span style="font-weight: bold; font-size: 0.85rem; color: gray; text-transform: uppercase;">Meeting Passcode</span>
                    <div style="font-family: monospace; font-size: 1.25rem; font-weight: bold; color: #f59e0b;">
                        {{ $meetingDetails['passcode'] }}
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 0.5rem; flex-wrap: wrap;">
                    {{-- HOST START BUTTON --}}
                    <x-filament::button tag="a" href="{{ $meetingDetails['start_url'] }}" target="_blank" color="success" icon="heroicon-m-video-camera">
                        Start Meeting (Host)
                    </x-filament::button>

                    {{-- COPY TO CLIPBOARD BUTTON --}}
                    <div x-data="{ copied: false }">
                        <x-filament::button 
                            type="button" 
                            color="gray" 
                            icon="heroicon-m-clipboard-document" 
                            @click="navigator.clipboard.writeText('{{ $meetingDetails['join_url'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        >
                            <span x-text="copied ? 'Copied to Clipboard!' : 'Copy Student Join Link'"></span>
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>