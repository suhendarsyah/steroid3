<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProduksiPerUptExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Query hasil agregasi produksi per UPT
     * (sudah difilter tahun & role di Service)
     */
    protected $query;

    /**
     * @param Builder|\Illuminate\Database\Query\Builder $query
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Data yang akan ditulis ke Excel
     */
    public function collection(): Collection
    {
        return collect($this->query->get())->map(function ($row) {
            return [
                'UPT'             => $row->upt,
                'Total Produksi'  => $row->total_produksi,
            ];
        });
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'UPT',
            'Total Produksi',
        ];
    }
}
