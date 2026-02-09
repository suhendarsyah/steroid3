<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProduksiBulananChart extends ChartWidget
{
    protected ?string $heading = 'Produksi Bulanan';

    public array $labelsBulan = [];
    public array $dataBulanan = [];
    // public ?int $filter = null;


    // ========================= PUBLIC PROPERTY =========================
    // Akan diisi dari DashboardProduksiInteraktif
    public ?int $tahun = null;
    public ?int $komoditas = null;

    // ========================= FILTER DROPDOWN =========================
    protected function getFilters(): ?array
    {
        // return DB::table('data_teknis')
        //     ->selectRaw('YEAR(tanggal) as year')
        //     ->distinct()
        //     ->orderBy('year', 'desc')
        //     ->pluck('year', 'year') // [2026 => 2026, 2025 => 2025]
        //     ->toArray();

            return DB::table('data_teknis')
            ->selectRaw('YEAR(tanggal) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year','year')
            ->toArray();
    }

    // ========================= DATA GRAFIK =========================
    protected function getData(): array
    {
        // // gunakan property $tahun dari DashboardProduksiInteraktif, fallback ke tahun sekarang
        // $tahun = $this->tahun ?? date('Y');

        // $query = DB::table('data_teknis')
        //     ->join('objek_produksis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')
        //     ->whereYear('data_teknis.tanggal', $tahun);

        // // filter komoditas jika ada
        // if ($this->komoditas) {
        //     $query->where('objek_produksis.komoditas_id', $this->komoditas);
        // }

        // $user = Auth::user();

        // // jika belum login, tampilkan nol semua
        // if (! $user) {
        //     return [
        //         'datasets' => [
        //             ['label' => 'Produksi', 'data' => array_fill(0, 12, 0)],
        //         ],
        //         'labels' => ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        //     ];
        // }

        // // pembatasan data berdasarkan role
        // if ($user->hasRole('kabid') && !empty($user->bidang_id)) {
        //     $query->where('objek_produksis.master_bidang_id', $user->bidang_id);
        // }
        // if ($user->hasRole('upt') && !empty($user->upt_id)) {
        //     $query->where('objek_produksis.upt_id', $user->upt_id);
        // }

        // // ambil total per bulan
        // $dataPerBulan = $query
        //     ->selectRaw('MONTH(data_teknis.tanggal) as bulan, SUM(data_teknis.nilai) as total')
        //     ->groupByRaw('MONTH(data_teknis.tanggal)')
        //     ->pluck('total', 'bulan')
        //     ->toArray();

        // // pastikan 12 bulan
        // $dataChart = [];
        // for ($bulan = 1; $bulan <= 12; $bulan++) {
        //     $dataChart[] = $dataPerBulan[$bulan] ?? 0;
        // }

        // return [
        //     'datasets' => [
        //         [
        //             'label' => 'Total Produksi Tahun ' . $tahun,
        //             'data' => $dataChart,
        //         ],
        //     ],
        //     'labels' => ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        // ];

$tahun = $this->filter ?? date('Y');

        $dataPerBulan = DB::table('data_teknis')
            ->selectRaw('MONTH(tanggal) as bulan, SUM(nilai) as total')
            ->whereYear('tanggal', $tahun)
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total','bulan')
            ->toArray();

        $labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $data = [];
        for ($i=1;$i<=12;$i++){
            $data[] = $dataPerBulan[$i] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Produksi Tahun '.$tahun,
                    'data' => $data,
                ],
            ],
        ];



    }

    // ========================= TIPE CHART =========================
    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
{
    return auth()->user()?->hasAnyRole([
        'super_admin',
        'kepala_dinas',
    ]);
}
}
