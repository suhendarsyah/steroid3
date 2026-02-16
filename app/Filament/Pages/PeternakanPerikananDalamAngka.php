<?php

namespace App\Filament\Pages;

use App\Models\DataTeknis;
use App\Models\Upt;
use Filament\Pages\Page;
use BackedEnum;

class PeternakanPerikananDalamAngka extends Page
{
    protected string $view = 'filament.pages.peternakan-perikanan-dalam-angka';

    protected static ?string $title = 'Peternakan & Perikanan Dalam Angka';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    // =========================
    // STATE
    // =========================
    public int $tahun;

    // =========================
    // PRODUKSI (ARRAY PER KOMODITAS)
    // =========================
    public array $produksiTahunIni = [];
    public array $produksiTahunLalu = [];

    public float $trenProduksiPersen = 0;
    public string $trenProduksiLabel = '';

    public array $produksiPerBidang = [];

    // =========================
    // POPULASI
    // =========================
    public float $totalPopulasi = 0;

    // =========================
    // UPT
    // =========================
    public int $totalUpt = 0;
    public int $uptMelapor = 0;
    public int $uptAktif = 0;
    public float $cakupanPelaporan = 0;

    // =========================
    // RINGKASAN BIDANG
    // =========================
    public array $ringkasanPerBidang = [];

    // =========================
    // DATA GRAFIK
    // =========================
    public array $produksiBulanan = [];

    // =========================
    // KONFIG KEGIATAN
    // =========================
    protected array $kegiatanProduksi = [5, 6];
    protected array $kegiatanPopulasi = [3, 7];

    // =========================
    // INIT
    // =========================
    public function mount(): void
    {
        $this->tahun = now()->year;
        $this->hitungData();
    }

    // =========================
    // LOGIKA UTAMA
    // =========================
    public function hitungData(): void
    {
        /**
         * =====================================================
         * PRODUKSI TAHUN INI (PER KOMODITAS & SATUAN)
         * =====================================================
         */
        $this->produksiTahunIni = DataTeknis::query()
            ->whereYear('data_teknis.tanggal', $this->tahun)
            ->whereIn('data_teknis.kegiatan_id', $this->kegiatanProduksi)
            ->join('objek_produksis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')
            ->join('komoditas', 'objek_produksis.komoditas_id', '=', 'komoditas.id')
            ->selectRaw('
                komoditas.nama,
                komoditas.satuan_default,
                SUM(data_teknis.nilai) as total
            ')
            ->groupBy('komoditas.nama', 'komoditas.satuan_default')
            ->get()
            ->map(fn ($r) => [
                'nama' => $r->nama,
                'satuan_default' => $r->satuan_default,
                'total' => (float) $r->total,
            ])
            ->toArray();

        /**
         * =====================================================
         * PRODUKSI TAHUN LALU (HANYA UNTUK TREND GLOBAL)
         * Catatan: tidak menjumlah lintas satuan untuk output,
         * hanya indikator tren makro.
         * =====================================================
         */
        $totalIniGlobal = DataTeknis::query()
            ->whereYear('tanggal', $this->tahun)
            ->whereIn('kegiatan_id', $this->kegiatanProduksi)
            ->sum('nilai');

        $totalLaluGlobal = DataTeknis::query()
            ->whereYear('tanggal', $this->tahun - 1)
            ->whereIn('kegiatan_id', $this->kegiatanProduksi)
            ->sum('nilai');

        if ($totalLaluGlobal > 0) {
            $this->trenProduksiPersen =
                (($totalIniGlobal - $totalLaluGlobal) / $totalLaluGlobal) * 100;
        } else {
            $this->trenProduksiPersen = 0;
        }

        if ($this->trenProduksiPersen > 1) {
            $this->trenProduksiLabel = 'Produksi meningkat';
        } elseif ($this->trenProduksiPersen < -1) {
            $this->trenProduksiLabel = 'Produksi menurun';
        } else {
            $this->trenProduksiLabel = 'Produksi relatif stabil';
        }

        /**
         * =====================================================
         * POPULASI
         * =====================================================
         */
        $this->totalPopulasi = DataTeknis::query()
            ->whereYear('tanggal', $this->tahun)
            ->whereIn('kegiatan_id', $this->kegiatanPopulasi)
            ->sum('nilai');

        /**
         * =====================================================
         * UPT
         * =====================================================
         */
        $this->totalUpt = Upt::count();

        $this->uptMelapor = DataTeknis::query()
            ->whereYear('tanggal', $this->tahun)
            ->distinct('upt_id')
            ->count('upt_id');

        $this->uptAktif = $this->uptMelapor;

        $this->cakupanPelaporan = $this->totalUpt > 0
            ? ($this->uptMelapor / $this->totalUpt) * 100
            : 0;

        /**
         * =====================================================
         * RINGKASAN PER BIDANG
         * =====================================================
         */
        $this->ringkasanPerBidang = DataTeknis::query()
            ->whereYear('tanggal', $this->tahun)
            ->join('master_kegiatan_teknis', 'data_teknis.kegiatan_id', '=', 'master_kegiatan_teknis.id')
            ->join('master_bidang', 'master_kegiatan_teknis.bidang_id', '=', 'master_bidang.id')
            ->selectRaw('master_bidang.nama as nama_bidang, SUM(data_teknis.nilai) as total_nilai')
            ->groupBy('master_bidang.nama')
            ->get()
            ->map(fn ($row) => [
                'nama'   => $row->nama_bidang,
                'status' => $row->total_nilai > 0 ? 'Aktif' : 'Belum Ada Data',
                'color'  => $row->total_nilai > 0 ? 'text-green-600' : 'text-red-600',
            ])
            ->toArray();

        /**
         * =====================================================
         * PRODUKSI BULANAN
         * =====================================================
         */
        $this->produksiBulanan = DataTeknis::query()
            ->whereYear('data_teknis.tanggal', $this->tahun)
            ->whereIn('data_teknis.kegiatan_id', $this->kegiatanProduksi)
            ->join('objek_produksis','data_teknis.objek_produksi_id','=','objek_produksis.id')
            ->join('komoditas','objek_produksis.komoditas_id','=','komoditas.id')
            ->selectRaw('
                komoditas.nama as komoditas,
                MONTH(data_teknis.tanggal) as bulan,
                SUM(data_teknis.nilai) as total
            ')
            ->groupBy('komoditas.nama','bulan')
            ->get()
            ->groupBy('komoditas')
            ->map(function($items){

                $bulan = array_fill(0,12,0);

                foreach($items as $row){
                    $bulan[$row->bulan - 1] = (float)$row->total;
                }

                return array_values($bulan);
            })
            ->toArray();


        

        /**
         * =====================================================
         * PRODUKSI PER BIDANG
         * =====================================================
         */
        $this->produksiPerBidang = DataTeknis::query()
            ->whereYear('data_teknis.tanggal', $this->tahun)
            ->join('master_kegiatan_teknis', 'data_teknis.kegiatan_id', '=', 'master_kegiatan_teknis.id')
            ->join('master_bidang', 'master_kegiatan_teknis.bidang_id', '=', 'master_bidang.id')
            ->selectRaw('master_bidang.nama as bidang, SUM(data_teknis.nilai) as total')
            ->groupBy('master_bidang.nama')
            ->pluck('total', 'bidang')
            ->toArray();
    }

    // =========================
    // AKSES
    // =========================
    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            'kepala_dinas',
            'super_admin',
        ]) ?? false;
    }
}
