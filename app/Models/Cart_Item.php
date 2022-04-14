<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart_Item extends Model
{   
    use SoftDeletes;
    use HasFactory;

    protected $table = 'cart_items';

}
