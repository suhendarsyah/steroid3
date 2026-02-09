<x-filament::page>
    <div class="space-y-6">

        {{-- =========================
            RINGKASAN SIABIDIBA (SINKRON TABLE)
        ========================= --}}
        @php
            $records = $this->getTableRecords();

            $jumlahData = $records->count();
            $totalNilai = $records->sum('nilai');

            $sample = $records->first();

            $bidang = optional($sample?->kegiatan?->bidang)->nama;
            $kegiatan = optional($sample?->kegiatan)->nama;
            $komoditas = optional($sample?->objekProduksi?->komoditas)->nama;
            $upt = optional($sample?->objekProduksi?->upt)->nama;

            $minTanggal = $records->min('tanggal');
            $maxTanggal = $records->max('tanggal');
        @endphp

        <x-filament::card class="bg-gray-50">
            <h3 class="text-sm font-semibold mb-2">
                Pusat Data Teknis
            </h3>

            <div class="text-sm space-y-1">
                <div>
                    <strong>Penanggung Jawab:</strong>
                    {{ $upt ?? 'Semua UPT' }}
                </div>

                <div>
                    <strong>Kegiatan :</strong>
                    {{ $kegiatan ?? 'Semua Kegiatan' }}
                    @if($bidang)
                        (Urusan {{ $bidang }})
                    @endif
                    @if($komoditas)
                        – Komoditas {{ $komoditas }}
                    @endif
                </div>

                <div>
                    <strong>Waktu Pelaksanaan</strong>
                    @if($minTanggal && $maxTanggal)
                        {{ \Carbon\Carbon::parse($minTanggal)->format('d M Y') }}
                        –
                        {{ \Carbon\Carbon::parse($maxTanggal)->format('d M Y') }}
                    @else
                        Semua Periode
                    @endif
                </div>

                <div>
                    <strong>Result:</strong>
                    Total {{ number_format($totalNilai) }}
                    dari {{ $jumlahData }} data
                </div>
            </div>
        </x-filament::card>

        <x-filament::page>

    {{-- =========================
        STAT DATA TEKNIS (FILTERED)
    ========================= --}}
    {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <x-filament::card class="bg-primary-50">
            <div class="text-xs text-primary-700">Total Populasi / Produksi</div>
            <div class="text-3xl font-bold text-primary-800">
                {{ number_format($statTotalNilai) }}
            </div>
            <div class="text-xs text-primary-600">
                Mengikuti filter aktif
            </div>
        </x-filament::card>

        <x-filament::card class="bg-green-50">
            <div class="text-xs text-green-700">Pelaku Usaha</div>
            <div class="text-3xl font-bold text-green-800">
                {{ number_format($statPelaku) }}
            </div>
            <div class="text-xs text-green-600">
                Peternak / Nelayan
            </div>
        </x-filament::card>

        <x-filament::card class="bg-blue-50">
            <div class="text-xs text-blue-700">Unit Usaha</div>
            <div class="text-3xl font-bold text-blue-800">
                {{ number_format($statUnitUsaha) }}
            </div>
            <div class="text-xs text-blue-600">
                Kandang / Kolam
            </div>
        </x-filament::card>

        <x-filament::card class="bg-yellow-50">
            <div class="text-xs text-yellow-700">Jumlah Laporan</div>
            <div class="text-3xl font-bold text-yellow-800">
                {{ number_format($statJumlahLaporan) }}
            </div>
            <div class="text-xs text-yellow-600">
                Data masuk
            </div>
        </x-filament::card>

    </div> --}}

    {{-- TABEL DATA TEKNIS --}}
    {{-- {{ $this->table }} --}}

</x-filament::page>


        {{-- =========================
            TABEL DATA TEKNIS
        ========================= --}}
        {{ $this->table }}


        {{-- =========================
    ZONA PELAKU USAHA (LIVE)
========================= --}}
<x-filament::card class="mt-8">

    <h3 class="text-sm font-semibold mb-3">
        Pelaku Usaha (By Name & Address)
    </h3>

    {{-- FILTER --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

        <div>
            <label class="text-xs text-gray-500">Kecamatan</label>
            <select wire:model="filterKecamatan" class="filament-forms-select mt-1">
                <option value="">Semua Kecamatan</option>
                @foreach(\App\Models\Kecamatan::all() as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs text-gray-500">Komoditas</label>
            <select wire:model="filterKomoditas" class="filament-forms-select mt-1">
                <option value="">Semua Komoditas</option>
                @foreach(\App\Models\Komoditas::all() as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>

    </div>

    {{-- INFO --}}
    <p class="text-xs text-gray-500 mb-2">
        Menampilkan {{ $totalPelaku }} pelaku usaha sesuai filter.
    </p>

    {{-- TABEL --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Nama</th>
                    <th class="px-3 py-2 text-left">Alamat</th>
                    <th class="px-3 py-2 text-left">Desa</th>
                    <th class="px-3 py-2 text-left">Kecamatan</th>
                    <th class="px-3 py-2 text-center">Unit Usaha</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($pelakuUsaha as $p)
                    <tr>
                        <td class="px-3 py-2 font-medium">{{ $p['nama'] }}</td>
                        <td class="px-3 py-2">{{ $p['alamat'] }}</td>
                        <td class="px-3 py-2">{{ $p['desa'] }}</td>
                        <td class="px-3 py-2">{{ $p['kecamatan'] }}</td>
                        <td class="px-3 py-2 text-center">{{ $p['unit_usaha'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                            Tidak ada data sesuai filter
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-filament::card>



        
        



    </div>
</x-filament::page>
