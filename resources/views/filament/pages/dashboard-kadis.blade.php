<x-filament-panels::page>
    {{-- Page content --}}
    <h2 class="text-xl font-bold mb-4">
        Dashboard Kepala Dinas
    </h2>

   <div wire:loading.delay
     wire:target="filtersForm"
     class="fixed top-4 right-4 z-50
            bg-primary-600 text-white
            px-3 py-2 rounded-lg shadow-lg text-xs
            animate-pulse">

    🔄 Memperbarui Dashboard...
</div>



    <p>Ringkasan capaian dan evaluasi kinerja.</p>
</x-filament-panels::page>
