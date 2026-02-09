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
    public array $pelakuUsaha = [];
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
        ]);
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

                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai')
                    ->numeric(),

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

    protected function loadPelakuUsaha(): void
        {
            $query = \App\Models\Pemilik::query()
                ->with([
                    'desa.kecamatan',
                    'objekProduksis.komoditas',
                ])
                ->whereHas('objekProduksis');

            // =========================
            // FILTER KECAMATAN
            // =========================
            if ($this->filterKecamatan) {
                $query->whereHas('desa', fn ($q) =>
                    $q->where('kecamatan_id', $this->filterKecamatan)
                );
            }

            // =========================
            // FILTER KOMODITAS
            // =========================
            if ($this->filterKomoditas) {
                $query->whereHas('objekProduksis', fn ($q) =>
                    $q->where('komoditas_id', $this->filterKomoditas)
                );
            }

            $rows = $query->get();

            // $this->pelakuUsaha = $rows->map(function ($p) {
            //     return [
            //         'nama'       => $p->nama,
            //         'alamat'     => $p->alamat,
            //         'desa'       => $p->desa->nama ?? '-',
            //         'kecamatan'  => $p->desa->kecamatan->nama ?? '-',
            //         'unit_usaha' => $p->objekProduksis->count(),
            //     ];
            // })->toArray();
            $this->pelakuUsaha = [
        [
            'nama' => 'TEST NAMA',
            'alamat' => 'TEST ALAMAT',
            'desa' => 'TEST DESA',
            'kecamatan' => 'TEST KECAMATAN',
            'komoditas' => 'TEST KOMODITAS',
            'unit_usaha' => 1,
        ],
    ];

    $this->totalPelaku = 1;
        }


    protected function afterTableFiltersApplied(): void
        {
            $this->loadPelakuUsaha();
            $this->hitungStatFiltered();
        }


    public function hitungPelakuUsaha(): void
        {
            $filters = $this->getTableFiltersForm()?->getState() ?? [];

            $query = DataTeknis::query()
                ->join('objek_produksis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')
                ->join('pemiliks', 'objek_produksis.pemilik_id', '=', 'pemiliks.id')
                ->leftJoin('desas', 'pemiliks.desa_id', '=', 'desas.id');

            // =========================
            // TERAPKAN FILTER YANG SAMA
            // =========================

            if (!empty($filters['bidang'])) {
                $query->whereHas('kegiatan', fn ($q) =>
                    $q->where('bidang_id', $filters['bidang'])
                );
            }

            if (!empty($filters['kegiatan'])) {
                $query->where('data_teknis.kegiatan_id', $filters['kegiatan']);
            }

            if (!empty($filters['komoditas'])) {
                $query->where('objek_produksis.komoditas_id', $filters['komoditas']);
            }

            if (!empty($filters['upt'])) {
                $query->where('objek_produksis.upt_id', $filters['upt']);
            }

            if (!empty($filters['tanggal']['dari'])) {
                $query->whereDate('data_teknis.tanggal', '>=', $filters['tanggal']['dari']);
            }

            if (!empty($filters['tanggal']['sampai'])) {
                $query->whereDate('data_teknis.tanggal', '<=', $filters['tanggal']['sampai']);
            }

            // =========================
            // AGREGASI PER PELAKU
            // =========================
            $rows = $query
                ->selectRaw('
                    pemiliks.id,
                    pemiliks.nama,
                    pemiliks.alamat,
                    desas.nama as desa,
                    COUNT(DISTINCT objek_produksis.id) as unit_usaha,
                    COUNT(data_teknis.id) as jumlah_laporan
                ')
                ->groupBy('pemiliks.id', 'pemiliks.nama', 'pemiliks.alamat', 'desas.nama')
                ->orderByDesc('jumlah_laporan')
                ->get();

            $this->pelakuUsaha = $rows->toArray();
            $this->totalPelaku = $rows->count();
        }

        public function updated($property): void
        {
            if (in_array($property, ['filterKecamatan', 'filterKomoditas'])) {
                $this->loadPelakuUsaha();
            }
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


       












}
