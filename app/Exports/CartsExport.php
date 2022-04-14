<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Models\Cart;

class CartsExport implements WithMultipleSheets
{
    use Exportable;

 

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $carts = Cart::all();

        foreach ($carts as $cart) {
            $sheets[] = new Sheets\CartItemSheet($cart);
        }

        return $sheets;
    }
}