<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;

use App\Http\Resources\Cart_resource;
use App\Http\Resources\Item_resource;

use App\Http\Controllers\Api\ApiController as ApiController;

use Illuminate\Http\Request;

class CartController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Visualizza tutti i carrelli
    public function index()
    {
        //prendiamo tutte le righe della tabella "carts"
        $carts = Cart::all();

        if(sizeof($carts) == 0){
            $message = "Empty cart list";
        }else{
            $message = "Cart list retrieved";
        }

        //response_maker($success,$result,$message,$code)
        return $this->response_maker(true,Cart_resource::collection($carts),$message,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

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
        if($found_items > 0){
            $cart = new Cart;
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
        }else{
            $data = [
                'Created cart' => [],
                'Found items' => $found_items."/".$number_items,
                'Added items' => Item_resource::collection($items)
            ];
            $message = "Cart can't be added! No items found!";
            $success = false;
            $code = 400; //bad request
        }
        

        return $this->response_maker($success,$data,$message,$code);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */

    //visualizza un carrello
    public function show($cart_id)
    {
        $cart = Cart::find($cart_id);
        
        if($cart != null){
            if($cart->count() > 0){
                $success = true;
                $data = new Cart_resource($cart);
                $message = "Cart found!";
                $code = 200;
            }
        }else{
            $success = false;
            $data = [];
            $message = "Cart not found!";
            $code = 404;
        }
        
        return $this->response_maker($success,$data,$message,$code);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
