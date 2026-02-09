<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataTeknisMentahExport implements FromCollection, WithHeadings
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection(): Collection
    {
        return $this->query->get()->map(function ($dt) {
            return [
                'Tanggal'  => $dt->tanggal,
                'UPT'      => $dt->objekProduksi?->upt?->nama,
                'Kegiatan' => $dt->kegiatan?->nama,
                'Jumlah'    => $dt->nilai,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'UPT',
            'Kegiatan',
            'Jumlah',
        ];
    }
}
