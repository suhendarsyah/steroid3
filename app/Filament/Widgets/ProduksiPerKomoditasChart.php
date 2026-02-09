<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;


class ProduksiPerKomoditasChart extends ChartWidget
{
    protected ?string $heading = 'Produksi Per Komoditas Chart';

    protected function getData(): array
    {

    $tahun = $this->filter ?? date('Y');

        $user = Auth::user();
        // $user = auth()->user();
        

        $baseQuery = DB::table('data_teknis')
            ->join('objek_produksis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')
            ->join('komoditas', 'objek_produksis.komoditas_id', '=', 'komoditas.id')
            ->whereYear('data_teknis.tanggal', $tahun);

        // // pembatasan role
        // if ($user->hasRole('kabid') && !empty($user->bidang_id)) {
        //     $baseQuery->where('objek_produksis.master_bidang_id', $user->bidang_id);
        // }

        // if ($user->hasRole('upt') && !empty($user->upt_id)) {
        //     $baseQuery->where('objek_produksis.upt_id', $user->upt_id);
        // }

        // ambil semua komoditas yang ada datanya di tahun ini
        $komoditasList = (clone $baseQuery)
            ->select('komoditas.id', 'komoditas.nama')
            ->distinct()
            ->orderBy('komoditas.nama')
            ->get();

        $datasets = [];
        foreach ($komoditasList as $komoditas) {
            $data = (clone $baseQuery)
                ->selectRaw('MONTH(data_teknis.tanggal) as bulan, SUM(data_teknis.nilai) as total')
                ->where('komoditas.id', $komoditas->id)
                ->groupByRaw('MONTH(data_teknis.tanggal)')
                ->pluck('total', 'bulan')
                ->toArray();

            $perBulan = [];
            for ($i = 1; $i <= 12; $i++) {
                $perBulan[] = $data[$i] ?? 0;
            }

            $datasets[] = [
                'label' => $komoditas->nama,
                'data' => $perBulan,
            ];
        }



        return [
             'datasets' => $datasets,
            'labels' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
{
    return auth()->user()?->hasAnyRole([
        'super_admin',
        'kepala_dinas',
    ]);
}
}
