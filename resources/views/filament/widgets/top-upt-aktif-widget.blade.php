<x-filament::section>
    <x-slot name="heading">
        Top 5 UPT Paling Aktif
    </x-slot>

    <div class="space-y-2">
        @foreach ($this->getTopUpt() as $index => $upt)
            <div class="flex justify-between border rounded-lg p-3">
                <div>
                    <span class="font-bold">
                        #{{ $index + 1 }} {{ $upt['nama'] }}
                    </span>
                </div>
                <div class="font-semibold">
                    {{ $upt['total'] }}
                </div>
            </div>
        @endforeach
    </div>
</x-filament::section>
