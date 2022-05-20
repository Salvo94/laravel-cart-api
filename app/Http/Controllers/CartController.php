<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use App\Models\Cart_Item;

use App\Http\Resources\Cart_resource;
use App\Http\Resources\Item_resource;

use App\Exports\CartsExport;
use App\Exports\CartItemsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Api\ApiController as ApiController;

use Illuminate\Http\Request;

class CartController extends ApiController
{
    //Visualizza tutti i carrelli
    public function index()
    {
        //prendiamo tutte le righe della tabella "carts"
        $carts = Cart::all();

        if (sizeof($carts) == 0) {
            $message = "Empty cart list";
        } else {
            $message = "Cart list retrieved";
        }
        $data = [
            'cart list' => Cart_resource::collection($carts)
        ];
        //response_maker($success,$result,$message,$code)
        return $this->response_maker(true, $data, $message, 200);
    }


    //Crea un carrello
    public function store(Request $request)
    {
        $product_id_list = $request->product_id_list;
        //cerca gli items dagli id inseriti nella richiesta
        $items = Item::find($product_id_list);

        //creazione variabili di controllo quantità
        $number_items = sizeOf($product_id_list);
        $found_items = $items->count();

        //verifica che ci sia almeno un item disponibile da inserire nel carrello
        //in caso negativo verrà restituito un errore e non sarà creato alcun carrello
        if ($found_items > 0) {
            $cart = new Cart();
            $cart->save();

            $cart->items()->attach($items);

            $data = [
                'Created cart' => new Cart_resource($cart),
                'Found items' => $found_items."/".$number_items,
                'Added items' => Item_resource::collection($items)
            ];

            $message = "cart successfully created";
            $success = true;
            $code = 201; //creato

            //in fine esportiamo la lista dei carrelli creati
            Excel::store(new CartsExport(), 'Created_carts.csv');
            //insieme alla lista dei prodotti inseriti nel carrello
            Excel::store(new CartItemsExport(), 'Cart_actions_history.csv');
        } else {
            $data = [
                'Created cart' => [],
                'Found items' => $found_items."/".$number_items,
                'Added items' => Item_resource::collection($items)
            ];

            $message = "Cart can't be added! No items found!";
            $success = false;
            $code = 400; //bad request
        }

        return $this->response_maker($success, $data, $message, $code);
    }


    //visualizza un carrello
    public function show($cart_id)
    {
        $cart = Cart::find($cart_id);

        if ($cart != null) {
            if ($cart->count() > 0) {
                $success = true;
                $message = "Cart found!";
                $code = 200;
            }
        } else {
            $success = false;
            $message = "Cart not found!";
            $code = 404;
        }

        $data = [
            "selected cart" => new Cart_resource($cart)
        ];

        return $this->response_maker($success, $data, $message, $code);
    }


    //visualizza gli items di un carrello
    public function show_items($cart_id)
    {
        $cart = Cart::find($cart_id);

        if ($cart != null) {
            if ($cart->count() > 0) {
                $success = true;
                $cart_items = $cart->items()->get();

                //un foreach che ci permette di simulare la visualizzazione di una tabella con delle righe soft deleted
                //questo perchè con i pivot non è possibile riconoscere le righe soft deleted, poichè bisognerbbe trattarli come dei model
                foreach ($cart_items as $i => $cart_item) {
                    if ($cart_items[$i]->pivot->deleted_at != null) {
                        unset($cart_items[$i]);
                    }
                }

                $items = Item_resource::collection($cart_items);
                $message = "Cart found!";
                $code = 200;
            }
        } else {
            $success = false;
            $items = "";
            $message = "Can't show items; Cart not found!";
            $code = 404;
        }

        $data = [
            "cart items" => $items
        ];

        return $this->response_maker($success, $data, $message, $code);
    }


    //Aggiungi elementi al carrello esistente
    public function add_items($cart_id, Request $request)
    {
        //verica l'esistenza del carrello
        $cart = Cart::find($cart_id);

        if ($cart != null) {
            $product_id_list = $request->product_id_list;
            //cerca gli items dagli id inseriti nella richiesta
            $items = Item::find($product_id_list);

            //creazione variabili di controllo quantità
            $number_items = sizeOf($product_id_list);
            $found_items = $items->count();

            //verifica che ci sia almeno un item disponibile da inserire nel carrello
            //in caso negativo verrà restituito un errore e non sarà aggiunto l'item al carrello
            if ($found_items > 0) {
                $cart->items()->attach($items);

                $data = [
                    'Selected car' => new Cart_resource($cart),
                    'Found items' => $found_items."/".$number_items,
                    'Added items' => Item_resource::collection($items)
                ];

                $message = "Item succesfully added to the cart";
                $success = true;
                $code = 201; //creato

                //esportiamo la lista aggiornata dello storico articoli nei carrelli
                Excel::store(new CartItemsExport(), 'Cart_actions_history.csv');
            } else {
                $data = [
                    'Selected cart' => $cart,
                    'Found items' => $found_items."/".$number_items,
                    'Added items' => Item_resource::collection($items)
                ];

                $message = "Items can't be added to the cart! No items found!";
                $success = false;
                $code = 400; //bad request
            }
        } else {
            $data = [
                'Selected cart' => []
            ];

            $message = "No cart found; Can't add any items";
            $success = false;
            $code = 400; //bad request
        }

        return $this->response_maker($success, $data, $message, $code);
    }


    public function remove_cart_item($cart_id, $pivot_id)
    {
        //verifichiamo che esista una relazione tra il pivot_id che abbiamo scelto e il cart_id
        $item = Cart_Item::where("id", $pivot_id)->where("cart_id", $cart_id)->first();

        if ($item != null) {
            //cancelliamo la relazione tra item e cart
            Cart_Item::where("id", $pivot_id)->where("cart_id", $cart_id)->first()->delete();

            //verifichiamo che esistano ancora items all'interno del carrelo, altrimenti cancelliamolo
            $cart = Cart::where("id", $cart_id)->first();

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

            $message = "Cart item succesfully removed!";
            $success = true;
            $code = 200;

            //stampiamo la lista aggiornata degllo storico degli articoli nei carrelli
            Excel::store(new CartItemsExport(), 'Cart_actions_history.csv');
        } else {
            $item = [];
            $message = "Can't remove item; No association found!";
            $success = false;
            $code = 400;
        }

        $data = [
            'Deleted item' => $item,
        ];

        return $this->response_maker($success, $data, $message, $code);
    }
}
