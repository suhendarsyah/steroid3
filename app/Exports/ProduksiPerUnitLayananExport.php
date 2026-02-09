<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProduksiPerUnitLayananExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection(): Collection
    {
        return collect($this->query->get())->map(function ($row) {
            return [
                'Unit Layanan'    => $row->unit_layanan,
                'UPT'             => $row->upt,
                'Total Produksi'  => $row->total_produksi,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Unit Layanan',
            'UPT',
            'Total Produksi',
        ];
    }
}
