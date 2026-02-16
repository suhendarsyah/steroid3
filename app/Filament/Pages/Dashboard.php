<?php

namespace App\Filament\Pages;

use BackedEnum;
use Illuminate\Support\Facades\Auth;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

use Filament\Actions\Action;

use App\Models\Bidang;
use App\Models\Upt;
use App\Models\DataTeknis;
use App\Models\ObjekProduksi;


class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    /**
     * 🔵 ICON NAVIGASI
     */
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    /**
     * 🔵 FILTER GLOBAL DASHBOARD
     */
public function filtersForm(Schema $schema): Schema
{
    $user = auth()->user();

    $fields = [];

    /**
     * 👑 SUPER ADMIN / KEPALA DINAS / PERENCANAAN
     */
    if ($user?->hasAnyRole(['super_admin','kepala_dinas','perencanaan'])) {

        $fields[] = Select::make('bidang_id')
            ->label('Bidang')
            ->options(\App\Models\Bidang::pluck('nama','id'))
            ->searchable()
            ->preload()
            ->live();

        $fields[] = Select::make('upt_id')
            ->label('UPT')
            ->options(\App\Models\Upt::pluck('nama','id'))
            ->searchable()
            ->preload()
            ->live();

        $fields[] = Select::make('objek_produksi_id')
            ->label('Unit Usaha')
            ->options(\App\Models\ObjekProduksi::pluck('nama','id'))
            ->searchable()
            ->preload()
            ->live();
    }

    /**
     * 🔵 KEPALA BIDANG
     */
    elseif ($user?->hasRole('kepala_bidang')) {

        $fields[] = Select::make('upt_id')
            ->label('UPT')
//             ->options(
//                 // \App\Models\Upt::where('bidang_id', $user->bidang_id)
//                 //     ->pluck('nama','id')
            ->options(function () use ($user) {

                return Upt::whereIn('id', function ($q) use ($user) {

                    $q->select('data_teknis.upt_id')
                        ->from('data_teknis')
                        ->join(
                            'master_kegiatan_teknis',
                            'master_kegiatan_teknis.id',
                            '=',
                            'data_teknis.kegiatan_id'
                        )
                        ->where('master_kegiatan_teknis.bidang_id', $user->bidang_id);

                })->pluck('nama','id');
            })

            ->searchable()
            ->preload()
            ->live();

        $fields[] = Select::make('objek_produksi_id')
            ->label('Komoditas')
            ->options(function () use ($user) {

                return ObjekProduksi::whereIn('id', function ($q) use ($user) {

                    $q->select('data_teknis.objek_produksi_id')
                        ->from('data_teknis')
                        ->join(
                            'master_kegiatan_teknis',
                            'master_kegiatan_teknis.id',
                            '=',
                            'data_teknis.kegiatan_id'
                        )
                        ->where('master_kegiatan_teknis.bidang_id', $user->bidang_id);

                })->pluck('nama','id');
            })
            ->searchable()
            ->preload()
            ->live();

    }

    /**
     * 🟣 UPT
     * hanya komoditas & tanggal
     */
    elseif ($user?->hasRole('upt')) {

        $fields[] = Select::make('objek_produksi_id')
            ->label('Komoditas')
            // ->options(\App\Models\ObjekProduksi::pluck('nama','id'))
            ->options(function () use ($user) {
                return ObjekProduksi::whereIn('id', function ($q) use ($user) {
                    $q->select('objek_produksi_id')
                        ->from('data_teknis')
                        ->where('upt_id',$user->upt_id);
                        })
                        ->pluck('nama','id');
})
            ->searchable()
            ->preload()
            ->live();

        // $fields[] = Select::make('upt_id')
        //     ->label('UPT')
        //     ->options(function ($get) {

        //         $bidangId = $get('bidang_id');

        //         return Upt::query()
        //             ->when($bidangId, function ($query) use ($bidangId) {
        //                 $query->whereIn('id', function ($sub) use ($bidangId) {

        //                     $sub->select('data_teknis.upt_id')
        //                         ->from('data_teknis')
        //                         ->join('objek_produksi','objek_produksi.id','=','data_teknis.objek_produksi_id')
        //                         ->where('objek_produksi.bidang_id',$bidangId);
        //                 });
        //             })
        //             ->pluck('nama','id');
        //     })
        //     ->searchable()
        //     ->preload()
        //     ->live();

    }

    /**
     * 🗓 SEMUA ROLE BOLEH FILTER TANGGAL
     */
    $fields[] = DatePicker::make('startDate')
        ->label('Tanggal Mulai')
        ->native(false)
        ->live();

    $fields[] = DatePicker::make('endDate')
        ->label('Tanggal Akhir')
        ->native(false)
        ->live();

    return $schema
        ->components([
            Section::make('Filter Dashboard')
                ->schema($fields)
                ->columns([
                    'default' => 1,
                    'md' => 4,
                ])
                ->columnSpanFull(),
        ]);
}


    /**
     * 🔵 HEADER WIDGET
     */
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    /**
     * 🔵 WIDGET DASHBOARD BERDASARKAN ROLE
     */
   public function getWidgets(): array
{
    $user = auth()->user();


    if ($user?->hasRole('super_admin')) {
        return [
            \App\Filament\Widgets\StatKepalaDinasWidget::class, // total user & aktivitas
            \App\Filament\Widgets\TopUptAktifWidget::class,
        ];
    }
    /**
     * 
     * 👑 KEPALA DINAS (Strategis / Makro)
     */
    if ($user?->hasRole('kepala_dinas')) {
        return [
            
            \App\Filament\Widgets\StatKepalaDinasWidget::class,
            \App\Filament\Widgets\StatProduksiWidget::class,
            \App\Filament\Widgets\InsightKadisWidget::class,
            \App\Filament\Widgets\TopUptAktifWidget::class,
            \App\Filament\Widgets\ChartProduksiUpt::class,
            
            \App\Filament\Widgets\ChartTrenProduksiBulanan::class,
            
        ];
    }

    /**
     * 🟡 PERENCANAAN (Analitik)
     */
    if ($user?->hasRole('perencanaan')) {
        return [
            \App\Filament\Widgets\StatProduksiWidget::class,
            \App\Filament\Widgets\ChartTrenProduksiBulanan::class,
            \App\Filament\Widgets\ChartProduksiUpt::class,
        ];
    }

    /**
     * 🔵 KEPALA BIDANG (Taktis)
     */
    if ($user?->hasRole('kepala_bidang')) {
        return [
            \App\Filament\Widgets\StatKepalaDinasWidget::class,
             \App\Filament\Widgets\StatProduksiWidget::class,
            \App\Filament\Widgets\TopUptAktifWidget::class,
            \App\Filament\Widgets\ChartProduksiUpt::class,
             
        ];
    }

    /**
     * 🟣 UPT (Operasional)
     */
    if ($user?->hasRole('upt')) {

    $uptId = $user->upt_id;
        return [
            \App\Filament\Widgets\StatKepalaDinasWidget::class,
            \App\Filament\Widgets\StatProduksiWidget::class,
            \App\Filament\Widgets\ChartTrenProduksiBulanan::class,
        ];
    }

    return [];
}


    /**
     * 🔵 ROLE YANG BOLEH MELIHAT DASHBOARD
     */
    public static function canView(): bool
    {
        // $user = Auth::user();

        // return $user && $user->hasAnyRole([
        //     'super_admin',
        //     'kepala_dinas',
        // ]);

        return true;
    }

    /**
     * 🔵 LOAD DATA AWAL
     */
    public function mount(): void
    {
        // SAFE MODE
    }

    /**
     * 🔵 TITLE DINAMIS
     */
    public function getTitle(): string
    {
        $user = auth()->user();

        if ($user?->hasRole('kepala_dinas')) {
            return 'Dashboard Strategis Dinas';
        }

        if ($user?->hasRole('perencanaan')) {
            return 'Dashboard Analisis Perencanaan';
        }

        if ($user?->hasRole('super_admin')) {
            return 'Dashboard Monitoring Sistem';
        }

        return 'Dashboard';
    }

    /**
     * 🔵 SUBHEADING DINAMIS
     */
    public function getSubheading(): ?string
    {
        $user = auth()->user();

        if ($user?->hasRole('kepala_dinas')) {
            return 'Ringkasan kondisi peternakan dan perikanan secara strategis.';
        }

        if ($user?->hasRole('perencanaan')) {
            return 'Gunakan filter untuk analisis data lintas bidang dan waktu.';
        }

        if ($user?->hasRole('super_admin')) {
            return 'Monitoring aktivitas sistem dan penggunaan data.';
        }

        return null;
    }

    /**
     * 🔵 BADGE ROLE DI HEADER
     */
    public function getHeaderActions(): array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        $label = null;

        /**
         * 👑 SUPER ADMIN
         */
        if ($user->hasRole('super_admin')) {
            $label = 'SUPER ADMIN';
        }

        /**
         * 👑 KEPALA DINAS
         */
        elseif ($user->hasRole('kepala_dinas')) {
            $label = 'KEPALA DINAS';
        }

        /**
         * 🟡 PERENCANAAN
         */
        elseif ($user->hasRole('perencanaan')) {
            $label = 'PERENCANAAN';
        }

        /**
         * 🔵 KEPALA BIDANG
         */
        elseif ($user->hasRole('kepala_bidang')) {

            $namaBidang = $user->bidang->nama ?? '';

            $label = 'KEPALA BIDANG';
            if ($namaBidang) {
                $label .= ' - ' . strtoupper($namaBidang);
            }
        }

        /**
         * 🟣 UPT
         */
        elseif ($user->hasRole('upt')) {

            $namaUpt = $user->upt->nama ?? '';

            $label = 'UPT';
            if ($namaUpt) {
                $label .= ' - ' . strtoupper($namaUpt);
            }
        }

        if (!$label) {
            return [];
        }

        return [
            Action::make('roleBadge')
                ->label($label)
                ->color('gray')
                ->disabled(),
        ];
    }

    public function getColumns(): int | array
        {
            return [
                'default' => 1,
                'md' => 2,
                'xl' => 3,
            ];
        }



   


}
