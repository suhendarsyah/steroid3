<x-filament::widget>
    <x-filament::card>

        {{-- ========================= FILTER ========================= --}}
        <div class="flex flex-wrap gap-4 items-end mb-6 p-4 bg-gray-50 rounded-xl border">
            <div>
                <label class="text-sm text-gray-600">Tahun</label>
                <select wire:model.live="tahun" class="filament-input w-40">
                    @foreach ($daftarTahun as $tahunItem)
                        <option value="{{ $tahunItem }}">{{ $tahunItem }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm text-gray-600">Komoditas</label>
                <select wire:model.live="komoditas" class="filament-input w-56">
                    <option value="">Semua Komoditas</option>
                    @foreach ($daftarKomoditas as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ========================= RINGKASAN ANGKA ========================= --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-filament::card>
                <div class="text-sm text-gray-500">Total Produksi</div>
                <div class="text-3xl font-bold text-primary">{{ number_format($totalProduksi) }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500">Rata-rata Bulanan</div>
                <div class="text-3xl font-bold text-success">{{ number_format($rataRataBulanan) }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500">Bulan Tertinggi</div>
                <div class="text-3xl font-bold text-warning">{{ $bulanTertinggi ?? '-' }}</div>
            </x-filament::card>

            {{-- <x-filament::card>
                <div class="text-sm text-gray-500">UPT Aktif</div>
                <div class="text-3xl font-bold text-info">{{ $uptAktif }}</div>
            </x-filament::card> --}}
        </div>

        {{-- ========================= PRODUKSI BULANAN ========================= --}}
        <x-filament::card>
            <h2 class="text-lg font-semibold mb-4 text-gray-700">Tren Produksi Bulanan</h2>
            <canvas id="chartBulanan" wire:ignore></canvas>
        </x-filament::card>

        {{-- ========================= PRODUKSI PER UPT ========================= --}}
        @if(!empty($labelsUpt) && count($labelsUpt) > 0)
            <x-filament::card>
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Produksi per UPT</h2>
                <canvas id="chartUpt" wire:ignore></canvas>
            </x-filament::card>
        @endif

    </x-filament::card>

    {{-- ========================= SCRIPT CHART.JS ========================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {

            function renderCharts() {
                const labelsBulan = @json($labelsBulan);
                const dataBulanan = @json($dataBulanan);

                const labelsUpt = @json($labelsUpt);
                const dataUpt = @json($dataUpt);

                // PRODUKSI BULANAN
                if(labelsBulan.length > 0){
                    const ctx1 = document.getElementById('chartBulanan').getContext('2d');
                    if(window.chartBulananObj) window.chartBulananObj.destroy();
                    window.chartBulananObj = new Chart(ctx1, {
                        type: 'line',
                        data: {
                            labels: labelsBulan,
                            datasets: [{
                                label: 'Produksi Bulanan',
                                data: dataBulanan,
                                borderWidth: 3,
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: { responsive: true, scales: { y: { beginAtZero: true } } }
                    });
                }

                // PRODUKSI PER UPT
                if(labelsUpt.length > 0){
                    const ctx2 = document.getElementById('chartUpt').getContext('2d');
                    if(window.chartUptObj) window.chartUptObj.destroy();
                    window.chartUptObj = new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: labelsUpt,
                            datasets: [{
                                label: 'Produksi per UPT',
                                data: dataUpt,
                                backgroundColor: '#10B981'
                            }]
                        },
                        options: {
                            responsive: true,
                            indexAxis: 'y',
                            scales: { x: { beginAtZero: true } }
                        }
                    });
                }
            }

            renderCharts();

            // rerender chart saat Livewire update (misal ganti filter)
            Livewire.hook('message.processed', () => { renderCharts(); });
        });
    </script>
</x-filament::widget>
