<x-filament::page>
<div class="space-y-6">

    @php
    $records = $this->getTableRecords();

    $jumlahData = $records->count();
    $totalNilai = $records->sum('nilai');
    


    // =========================
    // UPT
    // =========================
    $uptList = $records
        ->pluck('objekProduksi.upt.nama')
        ->filter()
        ->unique()
        ->values();

    $upt = $uptList->count() === 1 ? $uptList->first() : null;

    // =========================
    // BIDANG
    // =========================
    $bidangList = $records
        ->pluck('kegiatan.bidang.nama')
        ->filter()
        ->unique()
        ->values();

    $bidang = $bidangList->count() === 1 ? $bidangList->first() : null;

    // =========================
    // KEGIATAN
    // =========================
    $kegiatanList = $records
        ->pluck('kegiatan.nama')
        ->filter()
        ->unique()
        ->values();

    $kegiatan = $kegiatanList->count() === 1 ? $kegiatanList->first() : null;

    // =========================
    // KOMODITAS
    // =========================
    $komoditasList = $records
        ->pluck('objekProduksi.komoditas.nama')
        ->filter()
        ->unique()
        ->values();

    $komoditas = $komoditasList->count() === 1 ? $komoditasList->first() : null;

    // =========================
    // TANGGAL
    // =========================
    $minTanggal = $records->min('tanggal');
    $maxTanggal = $records->max('tanggal');
@endphp


    {{-- CARD RINGKASAN --}}
    <x-filament::card class="bg-gray-50">
        <h3 class="text-sm font-semibold mb-2">
            Pusat Data Teknis
        </h3>

        <div class="text-sm space-y-1">
            <div><strong>Sumber Data:</strong> {{ $upt ?? 'Semua UPT' }}</div>

            <div>
                <strong>Kegiatan :</strong>
                {{ $kegiatan ?? 'Semua Kegiatan' }}
                @if($bidang) (Urusan {{ $bidang }}) @endif
                @if($komoditas) – Komoditas {{ $komoditas }} @endif
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
                
                @php
                    $resultByKomoditas = $records
                        ->groupBy(fn ($r) => optional($r->objekProduksi?->komoditas)->nama)
                        ->map(function ($rows) {

                            $komoditas = optional($rows->first()->objekProduksi?->komoditas);

                            return [
                                'total'  => $rows->sum('nilai'),
                                'satuan' => $komoditas?->satuan_default,
                            ];
                        });
                @endphp

                <strong>Result:</strong>

                @forelse($resultByKomoditas as $nama => $data)
                    <div>
                        {{ $nama ?? '-' }} :
                        {{ number_format($data['total']) }}
                        {{ $data['satuan'] }}
                    </div>
                @empty
                    -
                @endforelse

            </div>
        </div>
    </x-filament::card>

    {{-- TABLE --}}
    {{ $this->table }}

    <x-filament::card class="mt-8">

        <div class="fi-section-content-ctn overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">

            <table class="fi-ta-table w-full text-sm">
                <thead class="fi-ta-header">
                    <tr>
                        <th class="fi-ta-header-cell">Nama</th>
                        <th class="fi-ta-header-cell">Alamat</th>
                        <th class="fi-ta-header-cell">Desa</th>
                        <th class="fi-ta-header-cell">Kecamatan</th>
                        <th class="fi-ta-header-cell text-center">Unit Usaha</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($this->pelakuUsaha as $p)
                        <tr class="fi-ta-row hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium">{{ $p['nama'] }}</td>
                            <td class="px-4 py-2">{{ $p['alamat'] }}</td>
                            <td class="px-4 py-2">{{ $p['desa'] }}</td>
                            <td class="px-4 py-2">{{ $p['kecamatan'] }}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="fi-badge fi-badge-color-primary">
                                    {{ $p['unit_usaha'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">
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
