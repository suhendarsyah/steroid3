<x-filament-panels::page>

    {{-- 🔵 STAT ATAS --}}
    <x-filament-widgets::widgets
        :widgets="[
            \App\Filament\Widgets\StatKepalaDinasWidget::class,
        ]"
        class="mb-6"
    />

    {{-- 🔵 PRODUKSI --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <x-filament-widgets::widgets
            :widgets="[
                \App\Filament\Widgets\StatProduksiWidget::class,
                \App\Filament\Widgets\TopUptAktifWidget::class,
            ]"
        />
    </div>

    {{-- 🔵 CHART --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <x-filament-widgets::widgets
            :widgets="[
                \App\Filament\Widgets\ChartProduksiUpt::class,
                \App\Filament\Widgets\ChartTrenProduksiBulanan::class,
            ]"
        />
    </div>

</x-filament-panels::page>
