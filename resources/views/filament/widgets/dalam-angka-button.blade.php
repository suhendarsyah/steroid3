{{-- <x-filament-widgets::widget>
    <x-filament::section> --}}
        {{-- Widget content --}}
    {{-- </x-filament::section>
</x-filament-widgets::widget> --}}


<x-filament::card>
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold">
                Peternakan & Perikanan Dalam Angka
            </h2>
            <p class="text-sm text-gray-500">
                Ringkasan angka resmi & statistik dinas
            </p>
        </div>

        <a
            href="{{ \App\Filament\Pages\PeternakanPerikananDalamAngka::getUrl() }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700"
        >
            Lihat
        </a>
    </div>
</x-filament::card>

