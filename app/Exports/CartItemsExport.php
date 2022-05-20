<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\Cart;

class CartItemsExport implements FromCollection, WithMapping, WithHeadings
{
    public function headings(): array
    {
        return [
            'Id_carrello',
            'SKU',
            'Nome',
            'Prezzo',
            "Data inserimento nel carrrello",
            "Data rimozione dal carrello"
        ];
    }

    public function collection()
    {
        return Cart::withTrashed()->get();
    }

    public function map($carts): array
    {
        $row = [];
        $cart_items = $carts->items()->get();
        foreach ($cart_items as $cart_item) {
            $inner_row =[
                $carts->id,
                $cart_item->sku,
                $cart_item->name,
                $cart_item->price,
                $cart_item->pivot->created_at,
                $cart_item->pivot->deleted_at,

            ];
            array_push($row, $inner_row);
        }
        //$row = new Sheets\CartItemSheet($carts);

        return $row;
    }
}
