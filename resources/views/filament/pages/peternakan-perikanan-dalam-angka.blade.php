<x-filament::page>
    <div class="space-y-8">

        {{-- =========================================================
            A. HEADER NARATIF (EXECUTIVE SUMMARY)
        ========================================================= --}}
        <x-filament::card class="bg-gray-50">
            <h1 class="text-2xl font-bold">
                Peternakan & Perikanan Dalam Angka
            </h1>
            <p class="text-sm text-gray-600 mt-2">
                Ringkasan kondisi, capaian, dan aktivitas urusan peternakan
                dan perikanan sebagai dasar pengambilan kebijakan Kepala Dinas.
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Periode: Tahun {{ $tahun }}
            </p>
        </x-filament::card>

        {{-- =========================================================
            B. FILTER GLOBAL (TAHUN)
        ========================================================= --}}
        <x-filament::card>
            <div class="flex items-center gap-6">
                <div>
                    <label class="text-sm text-gray-500">Tahun Data</label>
                    <select
                        wire:model="tahun"
                        wire:change="hitungData"
                        class="filament-forms-select mt-1"
                    >
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="text-sm text-gray-500 mt-6">
                    Perubahan tahun akan memperbarui seluruh indikator di bawah.
                </div>
            </div>
        </x-filament::card>

        {{-- =========================================================
            C. KONDISI UMUM (HEALTH CHECK)
        ========================================================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <x-filament::card class="border-l-4 border-green-500">
                <div class="text-sm text-gray-500">Status Sistem</div>
                <div class="text-xl font-bold text-green-600 mt-1">
                    Aktif
                </div>
                <div class="text-xs text-gray-400">
                    Sistem menerima input data
                </div>
            </x-filament::card>

            <x-filament::card class="border-l-4 border-blue-500">
                <div class="text-sm text-gray-500">UPT Aktif</div>
                <div class="text-xl font-bold mt-1">
                    {{ $uptAktif }} UPT
                </div>
                <div class="text-xs text-gray-400">
                    Melakukan input pada tahun {{ $tahun }}
                </div>
            </x-filament::card>

            <x-filament::card class="border-l-4 border-yellow-500">
                <div class="text-sm text-gray-500">Perhatian</div>
                <div class="text-xl font-bold mt-1">
                    Monitoring
                </div>
                <div class="text-xs text-gray-400">
                    Sebagian UPT belum aktif penuh
                </div>
            </x-filament::card>

        </div>

        {{-- =========================================================
            D. ANGKA STRATEGIS (CORE INDICATORS)
        ========================================================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <x-filament::card class="bg-primary-50">
                <div class="text-sm text-gray-500">Produksi</div>
                <div class="space-y-1 text-sm">
                    @forelse($this->produksiTahunIni as $item)
                        <div class="flex justify-between">
                            <span>{{ $item['nama'] }}</span>
                            <span class="font-semibold">
                                {{ number_format($item['total']) }}
                                {{ $item['satuan_default'] }}
                            </span>
                        </div>
                    @empty
                        <div class="text-gray-400">
                            Belum ada data produksi
                        </div>
                    @endforelse
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500">Populasi (Indikatif)</div>
                <div class="text-3xl font-bold mt-1">
                    {{ number_format($totalPopulasi) }}
                </div>
                <div class="text-xs text-gray-400">
                    Berdasarkan kegiatan populasi tahun {{ $tahun }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500">Cakupan Pelaporan</div>
                <div class="text-3xl font-bold mt-1">
                    {{ number_format($cakupanPelaporan, 1) }}%
                </div>
                <div class="text-xs text-gray-400">
                    {{ $uptMelapor }} dari {{ $totalUpt }} UPT melapor
                </div>
            </x-filament::card>

        </div>

        {{-- =========================================================
            E. TREN & NARASI DATA
        ========================================================= --}}
        <x-filament::card>
            <h3 class="text-sm font-semibold">Analisis Tren Produksi</h3>

            <div class="mt-3 space-y-2 text-sm text-gray-600">
                <p>
                    <strong>{{ $trenProduksiLabel }}</strong>
                    dibandingkan tahun {{ $tahun - 1 }}.
                </p>

                <p>
                    Perubahan sebesar
                    <strong>{{ number_format($trenProduksiPersen, 1) }}%</strong>
                    dari total produksi tahun sebelumnya.
                </p>

                <p class="text-xs text-gray-400">
                    Analisis ini digunakan sebagai indikator awal kondisi produksi,
                    bukan penilaian kinerja unit.
                </p>
            </div>
        </x-filament::card>

        {{-- =========================================================
            E1. GRAFIK TREN PRODUKSI BULANAN
        ========================================================= --}}
        <x-filament::card>
            <h3 class="text-sm font-semibold mb-3">
                Tren Produksi Bulanan
            </h3>

            <div class="h-80">
                <canvas id="chartProduksiBulanan" wire:ignore></canvas>
            </div>

            <p class="text-xs text-gray-400 mt-2">
                Grafik merupakan indeks agregasi berbagai komoditas produksi.
                Tidak dimaksudkan sebagai perbandingan satuan produksi.
            </p>
        </x-filament::card>

        {{-- =========================================================
            F. RINGKASAN PER BIDANG
        ========================================================= --}}
        <x-filament::card>
            <h3 class="text-sm font-semibold mb-3">
                Ringkasan Per Bidang (Urusan)
            </h3>

            <div class="space-y-2 text-sm">
                @forelse ($ringkasanPerBidang as $bidang)
                    <div class="flex justify-between">
                        <span>{{ $bidang['nama'] }}</span>
                        <span class="font-medium {{ $bidang['color'] }}">
                            {{ $bidang['status'] }}
                        </span>
                    </div>
                @empty
                    <div class="text-gray-400">
                        Belum ada data per bidang.
                    </div>
                @endforelse
            </div>
        </x-filament::card>

        {{-- =========================================================
            E2. KOMPOSISI PRODUKSI PER BIDANG
        ========================================================= --}}
        <x-filament::card>
            <h3 class="text-sm font-semibold mb-3">
                Komposisi Produksi per Bidang
            </h3>

            <p class="text-xs text-gray-500 mb-2">
                Visual kontribusi masing-masing bidang terhadap total produksi tahun {{ $tahun }}.
            </p>

            <div class="h-80">
                <canvas id="chartProduksiBidang" wire:ignore></canvas>
            </div>
        </x-filament::card>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- ===================== CHART BULANAN ===================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const datasetsRaw = @json($produksiBulanan);

            const labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

            const datasets = Object.entries(datasetsRaw).map(([label, values]) => ({
                label: label + ' (Indeks)', // ⭐ tambahan label aman
                data: values,
                borderWidth: 2,
                tension: 0.3,
            }));

            new Chart(
                document.getElementById('chartProduksiBulanan'),
                {
                    type: 'line',
                    data: {
                        labels,
                        datasets
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                }
            );
        });
    </script>

    {{-- ===================== CHART BIDANG ===================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const dataBidang = @json($produksiPerBidang);

            const labels = Object.keys(dataBidang);
            const values = Object.values(dataBidang);

            new Chart(
                document.getElementById('chartProduksiBidang'),
                {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Produksi Bidang (Indeks Agregasi)',
                            data: values,
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,

                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },

                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        return 'Indeks Produksi: ' +
                                            ctx.parsed.y.toLocaleString();
                                    }
                                }
                            },

                            legend: {
                                labels: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },

                        scales: {
                            x: {
                                stacked: false
                            },
                            y: {
                                stacked: false,
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                }
            );
        });
    </script>

    @endpush

</x-filament::page>
