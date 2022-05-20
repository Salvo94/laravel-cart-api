<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'carts';


    public function items()
    {
        //relazione molti a molti -> ogni carrello può avere uno o più prodotti ed ogni prodotto può appartenere a uno o più carrelli
        //cart_items -> il custon name della tabella intermediaria
        return $this->belongsToMany(Item::class, "cart_items")->withPivot(["id","deleted_at"])->withTimestamps();
    }
}
