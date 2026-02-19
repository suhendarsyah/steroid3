<?php

namespace App\Filament\Pages;

use App\Models\DataTeknis;
use App\Models\Bidang;
use App\Models\MasterKegiatanTeknis;
use App\Models\Komoditas;
use App\Models\Upt;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use BackedEnum;

class PusatDataTeknis extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

        // =========================
    // PANEL PELAKU USAHA (BY NAME)
    // =========================
    // public array $pelakuUsaha = [];
    public int $totalPelaku = 0;
    public int $totalUnitUsaha = 0;
    public int $totalLaporan = 0;


    public ?int $filterKecamatan = null;
    public ?int $filterKomoditas = null;

    public float $statTotalNilai = 0;
    public int $statPelaku = 0;
    public int $statUnitUsaha = 0;
    public int $statJumlahLaporan = 0;
    


    protected static ?string $title = 'Pusat Data Teknis';
    protected string $view = 'filament.pages.pusat-data-teknis';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            'kepala_dinas',
            'super_admin',
        ])?? false;
    }

    // =========================
    // TABLE
    // =========================
    public function table(Table $table): Table
    {
        return $table
            ->query(
                DataTeknis::query()
                    ->with([
                        'kegiatan.bidang',
                        'objekProduksi.komoditas',
                        'objekProduksi.upt',
                    ])
                    ->latest('tanggal')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kegiatan.bidang.nama')
                    ->label('Bidang')
                    ->sortable(),

                Tables\Columns\TextColumn::make('kegiatan.nama')
                    ->label('Kegiatan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('objekProduksi.komoditas.nama')
                    ->label('Komoditas'),

                // Tables\Columns\TextColumn::make('nilai')
                //     ->label('Nilai')
                //     ->numeric(),

                Tables\Columns\TextColumn::make('nilai')
                    ->label('Jumlah')
                    ->formatStateUsing(function ($state, $record) {

                        $satuan = optional(
                            optional($record->objekProduksi)->komoditas
                        )->satuan_default ?? '';

                        return number_format($state) . ' ' . $satuan;
                    })
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {

                            $komoditas = optional(
                                optional($record->objekProduksi)->komoditas
                            );

                            $satuan = $komoditas?->satuan_default ?? '';

                            // jika satuan ekor → tanpa desimal
                            if (strtolower($satuan) === 'ekor') {
                                return number_format($state, 0, ',', '.') . ' ' . $satuan;
                            }

                            // selain ekor → tampilkan desimal asli
                            return number_format($state, 2, ',', '.') . ' ' . $satuan;
                        }),



                Tables\Columns\TextColumn::make('objekProduksi.upt.nama')
                    ->label('UPT'),
            ])
            ->filters([
                SelectFilter::make('bidang')
                    ->label('Bidang')
                    
                    ->options(Bidang::pluck('nama', 'id')->toArray())
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return $query;

                        return $query->whereHas('kegiatan', fn ($q) =>
                            $q->where('bidang_id', $data['value'])
                        );
                    }),

                SelectFilter::make('kegiatan')
                    ->label('Kegiatan')
                   
                    ->options(MasterKegiatanTeknis::pluck('nama', 'id')->toArray())
                    ->query(fn ($query, array $data) =>
                        empty($data['value'])
                            ? $query
                            : $query->where('kegiatan_id', $data['value'])
                    ),

                SelectFilter::make('komoditas')
                    ->label('Komoditas')
                    
                    ->options(Komoditas::pluck('nama', 'id')->toArray())
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return $query;

                        return $query->whereHas('objekProduksi', fn ($q) =>
                            $q->where('komoditas_id', $data['value'])
                        );
                    }),

                SelectFilter::make('upt')
                    ->label('UPT')
                    
                    ->options(Upt::pluck('nama', 'id')->toArray())
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return $query;

                        return $query->whereHas('objekProduksi', fn ($q) =>
                            $q->where('upt_id', $data['value'])
                        );
                    }),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari')->label('Dari'),
                        DatePicker::make('sampai')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'] ?? null,
                                fn ($q, $d) => $q->whereDate('tanggal', '>=', $d))
                            ->when($data['sampai'] ?? null,
                                fn ($q, $d) => $q->whereDate('tanggal', '<=', $d));
                    }),
            ])
            ->paginated([10, 25, 50]);

            
    }
 

    protected function hitungStatFiltered(): void
    {
        // AMBIL QUERY DARI TABLE (SUDAH TERFILTER)
        $query = $this->getTable()->getQuery();

        // CLONE SUPAYA AMAN
        $base = clone $query;

        $this->statTotalNilai = (float) (clone $base)->sum('data_teknis.nilai');

        $this->statJumlahLaporan = (int) (clone $base)->count('data_teknis.id');

        $this->statUnitUsaha = (int) (clone $base)
            ->distinct('data_teknis.objek_produksi_id')
            ->count('data_teknis.objek_produksi_id');

        $this->statPelaku = (int) (clone $base)
            ->join('objek_produksis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')
            ->distinct('objek_produksis.pemilik_id')
            ->count('objek_produksis.pemilik_id');
    }


    

    public function getPelakuUsahaProperty()
            {
                // 🔵 ambil DATA YANG SUDAH TERFILTER dari TABLE
                $records = $this->getTableRecords();

                return $records
                    ->map(function ($row) {

                        $pemilik = $row->objekProduksi?->pemilik;

                        return [
                            'nama'        => $pemilik?->nama,
                            'alamat'      => $pemilik?->alamat,
                            'desa'        => $pemilik?->desa?->nama,
                            'kecamatan'   => $pemilik?->desa?->kecamatan?->nama,
                            'unit_usaha'  => $row->objekProduksi?->nama,
                        ];
                    })
                    ->unique('nama')
                    ->values();
            }


    public function getTrendKomoditasProperty()
{
    $records = $this->getTableRecords();

    if ($records->isEmpty()) {
        return collect();
    }

    // ========================
    // PERIODE SEKARANG
    // ========================
    $minTanggal = $records->min('tanggal');
    $maxTanggal = $records->max('tanggal');

    $start = \Carbon\Carbon::parse($minTanggal);
    $end   = \Carbon\Carbon::parse($maxTanggal);

    $diffDays = $start->diffInDays($end) ?: 1;

    // ========================
    // PERIODE SEBELUMNYA
    // ========================
    $prevStart = $start->copy()->subDays($diffDays + 1);
    $prevEnd   = $start->copy()->subDay();

    // ========================
    // TOTAL SEKARANG
    // ========================
    $current = $records
        ->groupBy(fn ($r) => optional($r->objekProduksi?->komoditas)->nama)
        ->map(fn ($rows) => $rows->sum('nilai'));

    // ========================
    // QUERY PERIODE SEBELUMNYA
    // ========================
    $previousRecords = \App\Models\DataTeknis::query()
        ->whereDate('tanggal','>=',$prevStart)
        ->whereDate('tanggal','<=',$prevEnd)
        ->with('objekProduksi.komoditas')
        ->get();

    $previous = $previousRecords
        ->groupBy(fn ($r) => optional($r->objekProduksi?->komoditas)->nama)
        ->map(fn ($rows) => $rows->sum('nilai'));

    // ========================
    // HITUNG TREND
    // ========================
    return $current->map(function ($nilai, $nama) use ($previous) {

        $sebelumnya = $previous[$nama] ?? 0;

        if ($sebelumnya == 0) {
            $status = 'baru';
            $persen = null;
        } else {
            $diff   = $nilai - $sebelumnya;
            $persen = ($diff / $sebelumnya) * 100;

            if ($persen > 5) {
                $status = 'naik';
            } elseif ($persen < -5) {
                $status = 'turun';
            } else {
                $status = 'stabil';
            }
        }

        return [
            'nilai' => $nilai,
            'status' => $status,
            'persen' => $persen,
        ];
    });
}



}
