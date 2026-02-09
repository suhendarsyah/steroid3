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
                <div class="text-sm text-primary-700">
                    Total Produksi
                </div>
                <div class="text-3xl font-bold text-primary-800 mt-1">
                    {{ number_format($totalProduksi) }}
                </div>
                <div class="text-xs text-primary-600">
                    Akumulasi produksi tahun {{ $tahun }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500">
                    Populasi (Indikatif)
                </div>
                <div class="text-3xl font-bold mt-1">
                    {{ number_format($totalPopulasi) }}
                </div>
                <div class="text-xs text-gray-400">
                    Berdasarkan kegiatan populasi tahun {{ $tahun }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500">
                    Cakupan Pelaporan
                </div>
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
            <h3 class="text-sm font-semibold">
                Analisis Tren Produksi
            </h3>

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
            E1. GRAFIK TREN PRODUKSI BULANAN (INTERAKTIF)
        ========================================================= --}}
        <x-filament::card>
            <h3 class="text-sm font-semibold mb-3">
                Tren Produksi Bulanan
            </h3>

            <div class="h-80">
                <canvas id="chartProduksiBulanan" wire:ignore></canvas>
            </div>
        </x-filament::card>

        {{-- =========================================================
            F. RINGKASAN PER URUSAN / BIDANG
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
            G. ATENSI & RISIKO
        ========================================================= --}}
        <x-filament::card class="bg-red-50">
            <h3 class="text-sm font-semibold text-red-700">
                Poin Perlu Perhatian
            </h3>

            <ul class="text-sm text-red-600 mt-2 space-y-1">
                <li>• Tidak semua UPT aktif melakukan input data.</li>
                <li>• Diperlukan evaluasi dan penguatan koordinasi.</li>
            </ul>
        </x-filament::card>

        {{-- =========================================================
            H. ARAH LANJUTAN (CALL TO ACTION)
        ========================================================= --}}
        <div class="flex justify-end gap-3 pt-4">
            <a
                href="{{ url('/admin/data-teknis') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium
                       text-white bg-primary-600 rounded-lg hover:bg-primary-700"
            >
                Lihat Data Teknis
            </a>
        </div>




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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dataBulanan = @json($produksiBulanan);

            const labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            const values = labels.map((_, i) => dataBulanan[i + 1] ?? 0);

            new Chart(
                document.getElementById('chartProduksiBulanan'),
                {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Produksi',
                            data: values,
                            borderWidth: 2,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                }
            );
        });
    </script>

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
                    label: 'Total Produksi',
                    data: values,
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) =>
                                ctx.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        }
    );
});
</script>


    @endpush

</x-filament::page>
