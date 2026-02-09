<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProduksiPerKecamatanExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Collection $rows;

    /**
     * Export TIDAK BOLEH query sendiri
     * Data sudah disiapkan oleh Service
     */
    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Kecamatan',
            'Komoditas',
            'Total Produksi',
        ];
    }
}
