<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
  
    public function index()
    {
       
    }

    public function create()
    {
        $item = new Item;
        $item->sku = "PR004";
        $item->name = "jeans";
        $item->price = 30;
        $item->save();
        $items = Item::all();
        return $items;
    }
}
