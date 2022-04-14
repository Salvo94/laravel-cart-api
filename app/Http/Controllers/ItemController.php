<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Cart;

use Illuminate\Http\Request;

use App\Http\Resources\Item_resource;

use App\Http\Controllers\Api\ApiController as ApiController;

use Illuminate\Support\Facades\Validator;

class ItemController extends ApiController
{
  
    public function index()
    {
        //prendiamo tutte le righe della tabella "items"
        $items = Item::all();

        if(sizeof($items) == 0){
            $message = "Empty item list";
        }else{
            $message = "Item list retrieved";
        }

        $data = [
            "item list" => Item_resource::collection($items)
        ];
        
        //response_maker($success,$result,$message,$code)
        return $this->response_maker(true,$data,$message,200);
    }

    public function store(Request $request)
    {
        //validiamo i dati da inserire (amo questa Facade!)
        $validated = Validator::make($request->all(),[
            'sku' => 'required|unique:App\Models\Item,sku',
            'name' => 'required|String',
            'price' => 'required|Numeric'
        ]);

        if($validated->fails()){
            $success = false;
            $item = [];
            $message = $validated->messages();
            $code = 400;
        }else{
            $item = new Item;
            $item->sku = $request->sku;
            $item->name = $request->name;
            $item->price = $request->price;
            $item->save();
            $success = true;
            $message = "Item added correctly to database!";
            $code = 200;
        }
        $data = [
            'added item' => new Item_resource($item)
        ];
      
        return $this->response_maker($success,$data,$message,$code);

    }

    public function delete_item($item_id){
        //verifichiamo che esista l'item
        $item = Item::where("id",$item_id)->first();
        
        if($item != null){
            //cancelliamo la relazione tra l'item da cancellare e il carrello
            $item->carts()->detach();

            //cancelliamo i carrelli che restano senza items al loro interno
            $carts = Cart::all();
            foreach($carts as $cart){
                if(($cart->items())->count() == 0){
                    $cart->delete();
                }
            }

            //cancelliamo l'item
            Item::where("id",$item_id)->first()->delete();
            $message = "Item succesfully deleted from database";
            $success = true;
            $code = 200;             
        }else{
            $item = [];
            $message = "Can't remove item; No item found";
            $success = false;
            $code = 400;    
        }

        $data = [
            'Deleted item' => new Item_resource($item)
        ];
        
        return $this->response_maker($success,$data,$message,$code);
    }
}
