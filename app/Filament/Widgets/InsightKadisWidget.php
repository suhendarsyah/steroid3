<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

use App\Models\DataTeknis;
use Carbon\Carbon;

class InsightKadisWidget extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'filament.widgets.insight-kadis';

    // public function getColumnSpan(): int|string|array
    // {
    //     return [
    //         'default' => 1,
    //         'xl' => 1,
    //     ];
    // }

    public function getColumnSpan(): int|string|array
{
    return [
        'default' => 1,
        'xl' => 'full',
    ];
}

    

    public function getInsights(): array
    {
        $user = auth()->user();

        $filters = $this->filters ?? [];

        $startDate = $filters['startDate'] ?? null;
        $endDate   = $filters['endDate'] ?? null;

        $query = DataTeknis::query()->with(['upt.bidang','objekProduksi']);

        if ($startDate) $query->whereDate('tanggal','>=',$startDate);
        if ($endDate)   $query->whereDate('tanggal','<=',$endDate);

        $data = $query->get();

        $insights = [];

        /**
         * 🔥 Insight 1 — Total Aktivitas
         */
        $total = $data->count();

        if ($total > 0) {
            $insights[] = "🔥 Terdapat {$total} aktivitas produksi pada periode ini.";
        }

        /**
         * 📉 Insight 2 — Komoditas Dominan
         */
        $komoditas = $data
            ->groupBy(fn($row)=>optional($row->objekProduksi)->nama ?? 'Produksi')
            ->map(fn($items)=>$items->sum('nilai'))
            ->sortDesc()
            ->keys()
            ->first();

        if ($komoditas) {
            $insights[] = "📊 Komoditas dominan saat ini: {$komoditas}.";
        }

        /**
         * ⚠️ Insight 3 — UPT belum input 7 hari
         */
        $uptAktif7Hari = DataTeknis::whereDate('tanggal','>=', Carbon::now()->subDays(7))
            ->distinct('upt_id')
            ->pluck('upt_id');

        $totalUptAktif = $uptAktif7Hari->count();

        if ($totalUptAktif < 3) {
            $insights[] = "⚠️ Aktivitas UPT rendah dalam 7 hari terakhir.";
        }

        return $insights;
    }
}
