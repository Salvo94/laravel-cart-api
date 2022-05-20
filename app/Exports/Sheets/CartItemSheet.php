<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Cart;

class CartItemSheet implements FromCollection
{
    private $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $cart_items = ($this->cart)->items()->get();
        return $cart_items;
    }
}
