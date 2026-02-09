<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use App\Services\DownloadDataService;

class DownloadData extends Page implements HasForms
{
    use InteractsWithForms;

    /* =====================================================
     | ROUTE & VIEW
     ===================================================== */
    protected static ?string $slug = 'download-data';

    public function getView(): string
    {
        return 'filament.pages.download-data';
    }

    /* =====================================================
     | NAVIGASI
     ===================================================== */
    public static function getNavigationLabel(): string
    {
        return 'Download Data';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Laporan & Ekspor';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-arrow-down-tray';
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole([
            'super_admin',
            'kepala_dinas',
            'perencanaan',
            'kepala_bidang',
            'upt',
        ]) ?? false;
    }

    /* =====================================================
     | FORM STATE
     ===================================================== */
    public array $data = [];

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    /* =====================================================
     | FORM SCHEMA
     ===================================================== */
    protected function getFormSchema(): array
    {
        return [
            Select::make('jenis_data')
                ->label('Jenis Data')
                // ->options([
                //     'data_teknis_mentah'     => 'Data Teknis Mentah',
                //     'produksi_upt'           => 'Produksi per UPT',
                //     'produksi_unit_layanan'  => 'Produksi per Unit Layanan',
                //     'target_vs_realisasi'    => 'Target vs Realisasi',
                //     'produksi_kecamatan'     => 'Produksi per Kecamatan (Format BPS)',
                //     'populasi_ternak'        => 'Populasi Ternak per Wilayah',
                // ])
                ->options(fn() => $this->getJenisDataOptions())
                ->required(),

            Select::make('tahun')
                ->label('Tahun')
                ->options(
                    collect(range(now()->year - 5, now()->year))
                        ->mapWithKeys(fn($y) => [$y => $y])
                        ->toArray()
                )
                ->default(now()->year)
                ->required(),
        ];
    }

    /* =====================================================
     | FORM ACTIONS (INI KUNCI 404)
     ===================================================== */
    protected function getFormActions(): array
    {
        return [
            Action::make('download')
                ->label('Download Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action(fn() => $this->download()), // ⬅️ PENTING
        ];
    }

    /* =====================================================
     | LOGIC DOWNLOAD
     ===================================================== */
    public function download()
    {
        $state = $this->form->getState();

        return app(DownloadDataService::class)->download(
            $state['jenis_data'],
            (int) $state['tahun']
        );
    }

    protected function getJenisDataOptions(): array
    {
        $user = auth()->user();

        // OPSI DASAR (SEMUA ROLE)
        $options = [
            'data_teknis_mentah' => 'Data Teknis',
            'produksi_upt'       => 'Produksi per UPT',
            'populasi_ternak'    => 'Populasi Ternak per Wilayah',
        ];

        // ROLE STRATEGIS
        if ($user->hasAnyRole(['super_admin', 'kepala_dinas', 'perencanaan'])) {
            $options['produksi_unit_layanan'] = 'Produksi per Unit Layanan';
            $options['produksi_kecamatan']    = 'Produksi per Kecamatan (Format BPS)';
            $options['target_vs_realisasi']   = 'Target vs Realisasi';
        }

        // KEPALA BIDANG (TANPA BPS KECAMATAN)
        if ($user->hasRole('kepala_bidang')) {
            $options['produksi_unit_layanan'] = 'Produksi per Unit Layanan';
            $options['target_vs_realisasi']   = 'Target vs Realisasi';
        }

        return $options;
    }

    protected function getFormFooter(): ?string
    {
        return <<<HTML
    <div class="text-sm text-gray-500">
        <p><strong>Catatan:</strong></p>
        <ul class="list-disc pl-5">
            <li><strong>Populasi ternak</strong> = jumlah ekor (stok), bukan produksi.</li>
            <li><strong>Produksi</strong> berasal dari data teknis kegiatan.</li>
            <li>Beberapa jenis data hanya tersedia untuk role tertentu.</li>
        </ul>
    </div>
    HTML;
    }
}
