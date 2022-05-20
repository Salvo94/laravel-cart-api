<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\Cart;

class CartsExport implements FromCollection, WithMapping, WithHeadings
{
    public function headings(): array
    {
        return [
            'Id_carrello',
            "Data Creazione Carrello",
            "Data rimozione carrello"
        ];
    }

    public function collection()
    {
        return Cart::withTrashed()->get();
    }

    public function map($carts): array
    {
        return [
            $carts->id,
            $carts->created_at,
            $carts->deleted_at,
        ];
    }
}
