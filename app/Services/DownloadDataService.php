<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\DataTeknis;
use App\Models\Target;

use App\Services\DataQueryService;

use App\Exports\DataTeknisMentahExport;
use App\Exports\ProduksiPerUptExport;
use App\Exports\ProduksiPerUnitLayananExport;
use App\Exports\ProduksiPerKecamatanExport;
use App\Exports\TargetVsRealisasiExport;
use App\Exports\PopulasiTernakPerWilayahExport;

class DownloadDataService
{
    /**
     * =====================================================
     * SATU PINTU DOWNLOAD DATA
     * =====================================================
     */
    public function download(string $jenisData, int $tahun)
    {
        return match ($jenisData) {

            'data_teknis_mentah'
            => $this->handleDataTeknisMentah($tahun),

            'produksi_upt'
            => $this->handleProduksiPerUpt($tahun),

            'produksi_unit_layanan'
            => $this->handleProduksiPerUnitLayanan($tahun),

            'produksi_kecamatan'
            => $this->handleProduksiPerKecamatan($tahun),

            'target_vs_realisasi'
            => $this->handleTargetVsRealisasi($tahun),

            'populasi_ternak'
            => $this->handlePopulasiTernakPerWilayah(),

            default
            => abort(404, 'Jenis data tidak dikenali'),
        };
    }

    /**
     * =====================================================
     * 1️⃣ DATA TEKNIS MENTAH
     * =====================================================
     */
    protected function handleDataTeknisMentah(int $tahun)
    {
        $user = Auth::user();

        $query = DataTeknis::query()
            ->with(['kegiatan', 'objekProduksi.upt'])
            ->whereYear('tanggal', $tahun);

        // 🔒 FILTER ROLE
        if ($user->hasRole('upt')) {
            $query->whereHas(
                'objekProduksi',
                fn($q) =>
                $q->where('upt_id', $user->upt_id)
            );
        }

        if ($user->hasRole('kepala_bidang')) {
            $query->whereHas(
                'objekProduksi.upt',
                fn($q) =>
                $q->where('bidang_id', $user->bidang_id)
            );
        }

        return Excel::download(
            new DataTeknisMentahExport($query),
            "data_teknis_mentah_{$tahun}.xlsx"
        );
    }

    /**
     * =====================================================
     * 2️⃣ PRODUKSI PER UPT
     * =====================================================
     */
    protected function handleProduksiPerUpt(int $tahun)
    {
        $user = Auth::user();

        $query = DB::table('data_teknis as dt')
            ->join('objek_produksis as op', 'op.id', '=', 'dt.objek_produksi_id')
            ->join('upts as u', 'u.id', '=', 'op.upt_id')
            ->whereYear('dt.tanggal', $tahun)
            ->groupBy('u.id', 'u.nama')
            ->select(
                'u.nama as upt',
                DB::raw('SUM(dt.nilai) as total_produksi')
            );

        if ($user->hasRole('upt')) {
            $query->where('u.id', $user->upt_id);
        }

        if ($user->hasRole('kepala_bidang')) {
            $query->where('u.bidang_id', $user->bidang_id);
        }

        return Excel::download(
            new ProduksiPerUptExport($query),
            "produksi_per_upt_{$tahun}.xlsx"
        );
    }

    /**
     * =====================================================
     * 3️⃣ PRODUKSI PER UNIT LAYANAN
     * =====================================================
     */
    protected function handleProduksiPerUnitLayanan(int $tahun)
    {
        $user = Auth::user();

        $query = DB::table('data_teknis as dt')
            ->join('objek_produksis as op', 'op.id', '=', 'dt.objek_produksi_id')
            ->join('unit_layanans as ul', 'ul.id', '=', 'op.unit_layanan_id')
            ->join('upts as u', 'u.id', '=', 'op.upt_id')
            ->whereYear('dt.tanggal', $tahun)
            ->groupBy('ul.id', 'ul.nama', 'u.nama')
            ->select(
                'ul.nama as unit_layanan',
                'u.nama as upt',
                DB::raw('SUM(dt.nilai) as total_produksi')
            );

        if ($user->hasRole('upt')) {
            $query->where('u.id', $user->upt_id);
        }

        if ($user->hasRole('kepala_bidang')) {
            $query->where('u.bidang_id', $user->bidang_id);
        }

        return Excel::download(
            new ProduksiPerUnitLayananExport($query),
            "produksi_per_unit_layanan_{$tahun}.xlsx"
        );
    }

    /**
     * =====================================================
     * 4️⃣ PRODUKSI PER KECAMATAN (FORMAT BPS)
     * =====================================================
     */
    protected function handleProduksiPerKecamatan(int $tahun)
    {
        $user = Auth::user();

        // 🔒 UPT & Kepala Bidang TIDAK BOLEH
        if ($user->hasAnyRole(['upt', 'kepala_bidang'])) {
            abort(403);
        }

        $query = DataQueryService::produksiPerKecamatan($tahun);

        $rows = collect($query->get())->map(fn($row) => [
            'Tahun'          => $tahun,
            'Kecamatan'      => $row->kecamatan,
            'Komoditas'      => $row->komoditas,
            'Total Produksi' => $row->total_produksi,
        ]);

        return Excel::download(
            new ProduksiPerKecamatanExport($rows),
            "produksi_per_kecamatan_{$tahun}.xlsx"
        );
    }

    /**
     * =====================================================
     * 5️⃣ TARGET VS REALISASI
     * =====================================================
     */
    protected function handleTargetVsRealisasi(int $tahun)
    {
        $user = Auth::user();

        if ($user->hasRole('upt')) {
            abort(403);
        }

        $targets = Target::query()
            ->where('tahun', $tahun)
            ->with(['bidang', 'komoditas', 'kegiatan'])
            ->get();

        if ($user->hasRole('kepala_bidang')) {
            $targets = $targets->where('master_bidang_id', $user->bidang_id);
        }

        $rows = $targets->map(function ($target) {
            $realisasi = DB::table('data_teknis')
                ->whereYear('tanggal', $target->tahun)
                ->where('kegiatan_id', $target->master_kegiatan_teknis_id)
                ->sum('nilai');

            return [
                'Tahun'        => $target->tahun,
                'Bidang'       => $target->bidang?->nama,
                'Komoditas'    => $target->komoditas?->nama,
                'Kegiatan'     => $target->kegiatan?->nama,
                'Target'       => $target->target_jumlah,
                'Realisasi'    => $realisasi,
                'Persentase %' => $target->target_jumlah > 0
                    ? round(($realisasi / $target->target_jumlah) * 100, 2)
                    : 0,
            ];
        });

        return Excel::download(
            new TargetVsRealisasiExport($rows),
            "target_vs_realisasi_{$tahun}.xlsx"
        );
    }

    /**
     * =====================================================
     * 6️⃣ POPULASI TERNAK PER WILAYAH (UPT)
     * =====================================================
     */
    protected function handlePopulasiTernakPerWilayah()
    {
        $query = DataQueryService::populasiTernakPerWilayah();

        $rows = collect($query->get())->map(fn($row) => [
            'Wilayah (UPT)'   => $row->wilayah,
            'Komoditas'       => $row->komoditas,
            'Populasi (Ekor)' => $row->populasi,
        ]);

        return Excel::download(
            new PopulasiTernakPerWilayahExport($rows),
            'populasi_ternak_per_wilayah.xlsx'
        );
    }
}
