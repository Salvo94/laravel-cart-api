<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Cart;
use App\Models\Cart_Item;

use Illuminate\Http\Request;

use App\Http\Resources\Item_resource;

use App\Exports\CartsExport;
use App\Exports\CartItemsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Api\ApiController as ApiController;

use Illuminate\Support\Facades\Validator;

class ItemController extends ApiController
{
    public function index()
    {
        //prendiamo tutte le righe della tabella "items"
        $items = Item::all();

        if (sizeof($items) == 0) {
            $message = "Empty item list";
        } else {
            $message = "Item list retrieved";
        }

        $data = [
            "item list" => Item_resource::collection($items)
        ];

        //response_maker($success,$result,$message,$code)
        return $this->response_maker(true, $data, $message, 200);
    }

    public function store(Request $request)
    {
        //validiamo i dati da inserire (amo questa Facade!)
        $validated = Validator::make($request->all(), [
            'sku' => 'required|unique:App\Models\Item,sku',
            'name' => 'required|String',
            'price' => 'required|Numeric'
        ]);

        if ($validated->fails()) {
            $success = false;
            $item = [];
            $message = $validated->messages();
            $code = 400;
        } else {
            $item = new Item();
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

        return $this->response_maker($success, $data, $message, $code);
    }

    public function delete_item($item_id)
    {
        //verifichiamo che esista l'item
        $item = Item::where("id", $item_id)->first();

        if ($item != null) {
            //cancelliamo la relazione tra l'item da cancellare e il carrello
            Cart_item::where("item_id", $item_id)->delete();

            //cancelliamo i carrelli che restano senza items al loro interno
            $carts = Cart::all();
            foreach ($carts as $cart) {
                $cart_items = $cart->items()->get();

                //visto che con le tabelle pivot non è possibile non contare direttamente le righe soft deleted
                //si effettuerà un foreach dell'object che controllerà se c'è almeno una riga con la colonna deleted_at == null
                //in  modo tale da considerare il carrello non vuoto
                $empty_cart = true;

                foreach ($cart_items as $i => $cart_item) {
                    if ($cart_items[$i]->pivot->deleted_at == null) {
                        $empty_cart = false;
                    }
                }
                if ($empty_cart == true) {
                    $cart->delete();

                    //aggiorniamo ed esportiamo la lista dei carrelli per far comparire la data di rimozione del carrello cancellato
                    Excel::store(new CartsExport(), 'Created_carts.csv');
                }
            }

            //cancelliamo l'item
            Item::where("id", $item_id)->first()->delete();
            $message = "Item succesfully deleted from database";
            $success = true;
            $code = 200;

            //stampiamo la lista aggiornata degllo storico degli articoli nei carrelli
            Excel::store(new CartItemsExport(), 'Cart_actions_history.csv');
        } else {
            $item = [];
            $message = "Can't remove item; No item found";
            $success = false;
            $code = 400;
        }

        $data = [
            'Deleted item' => new Item_resource($item)
        ];

        return $this->response_maker($success, $data, $message, $code);
    }
}
