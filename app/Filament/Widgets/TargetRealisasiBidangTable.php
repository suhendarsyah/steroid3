<?php

namespace App\Filament\Widgets;

use App\Models\Target;
use App\Models\DataTeknis;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TargetRealisasiBidangTable extends TableWidget
{
    protected static ?string $heading = 'Target vs Realisasi Bidang';

    protected static ?int $sort = 2;

    /**
     * =====================================================
     * QUERY UTAMA (SUMBER BARIS TABEL)
     * =====================================================
     * - Diambil dari tabel targets
     * - Khusus target milik bidang user login
     */
    protected function getTableQuery(): Builder
    {
        return Target::query()
            ->where('master_bidang_id', auth()->user()->bidang_id)
            ->with(['komoditas', 'kegiatan']);
    }

    /**
     * =====================================================
     * DEFINISI KOLOM
     * =====================================================
     */
    protected function getTableColumns(): array
    {
        return [

            Tables\Columns\TextColumn::make('komoditas.nama')
                ->label('Komoditas')
                ->sortable(),

            Tables\Columns\TextColumn::make('kegiatan.nama')
                ->label('Kegiatan')
                ->sortable(),

            Tables\Columns\TextColumn::make('target_jumlah')
                ->label('Target')
                ->numeric()
                ->sortable(),

            /**
             * =================================================
             * REALISASI
             * =================================================
             * - SUM(data_teknis.nilai)
             * - Difilter:
             *   • tahun
             *   • kegiatan
             *   • bidang (via objek produksi → UPT)
             */
            Tables\Columns\TextColumn::make('realisasi')
                ->label('Realisasi')
                ->numeric()
                ->getStateUsing(function (Target $record) {

                    return DataTeknis::query()
                        ->whereYear('tanggal', $record->tahun)
                        ->where('kegiatan_id', $record->master_kegiatan_teknis_id)
                        ->whereHas('objekProduksi.upt', function ($q) use ($record) {
                            $q->where('bidang_id', $record->master_bidang_id);
                        })
                        ->sum('nilai');
                }),

            /**
             * =================================================
             * CAPAIAN (%)
             * =================================================
             * Rumus:
             * (realisasi / target) × 100
             *
             * Catatan kebijakan:
             * - Dibatasi max 100% (aman untuk dashboard)
             */
            Tables\Columns\TextColumn::make('capaian')
                ->label('Capaian (%)')
                ->getStateUsing(function (Target $record) {

                    if ($record->target_jumlah == 0) {
                        return '0 %';
                    }

                    $realisasi = DataTeknis::query()
                        ->whereYear('tanggal', $record->tahun)
                        ->where('kegiatan_id', $record->master_kegiatan_teknis_id)
                        ->whereHas('objekProduksi.upt', function ($q) use ($record) {
                            $q->where('bidang_id', $record->master_bidang_id);
                        })
                        ->sum('nilai');

                    // Hitungan asli
                    $capaian = ($realisasi / $record->target_jumlah) * 100;

                    // Kebijakan dashboard: max 100%
                    $capaian = min($capaian, 100);

                    return number_format($capaian, 1) . ' %';
                }),
        ];
    }

    /**
     * =====================================================
     * AKSES WIDGET
     * =====================================================
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kepala_bidang');
    }
}
