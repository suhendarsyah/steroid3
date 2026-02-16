<x-filament-widgets::widget>
    <x-filament::section>

        <div class="space-y-2 text-sm">
            @foreach ($this->getInsights() as $insight)
                <div class="px-3 py-2 rounded-lg bg-slate-900/40 ring-1 ring-amber-500/20">
                    {{ $insight }}
                </div>
            @endforeach
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
