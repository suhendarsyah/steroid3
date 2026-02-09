<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PopulasiTernakPerWilayahExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Data siap pakai (sudah diolah dari Service)
     */
    protected Collection $rows;

    /**
     * Export TIDAK melakukan query.
     * Data dikirim dari DownloadDataService.
     */
    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    /**
     * Data yang akan ditulis ke Excel
     */
    public function collection(): Collection
    {
        return $this->rows;
    }

    /**
     * Judul kolom Excel
     */
    public function headings(): array
    {
        return [
            'Wilayah (UPT)',
            'Komoditas',
            'Populasi (Ekor)',
        ];
    }
}
