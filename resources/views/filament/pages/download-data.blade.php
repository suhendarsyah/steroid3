<x-filament::page>
    <form wire:submit.prevent="download" class="space-y-6 max-w-xl">
        {{ $this->form }} <br>

        <x-filament::button
            type="submit"
            color="primary"
            icon="heroicon-o-arrow-down-tray"
        >
            Download Excel
        </x-filament::button>
    </form>
</x-filament::page>
