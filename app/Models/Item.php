<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Item extends Model
{
    use HasFactory;


    protected $table = 'items';
    
    public function carts()
    {
        //relazione molti a molti -> ogni prodotto può appartenere a uno o più carrelli ed ogni carrello può avere uno o più prodotti
        //cart_items -> il custon name della tabella intermediaria
        return $this->belongsToMany(Cart::class, "cart_items")->withPivot(["id","deleted_at"])->withTimestamps();
    }
}
