<?php

namespace App;


class Cart {
    public $items;
    public $totalQuantity;
    public $totalPrice;

    public function __construct($prevCart) {
        if($prevCart != null) {
            $this->items = $prevCart->items;
            $this->totalQuantity = $prevCart->totalQuantity;
            $this->totalPrice = $prevCart->totalPrice;
        } else {
            $this->items = [];
            $this->totalQuantity = 0;
            $this->totalPrice = 0;
        }
    }

    public function addItem($id, $product) {
        $price = (float) str_replace("$", "", $product->price);
        # item already exist
        if(array_key_exists($id, $this->items)){
            $productToAdd = $this->items[$id];
            $productToAdd['quantity']++;
            $productToAdd['totalSinglePrice'] = $productToAdd['quantity'] * $price;
        } else { //first time to add this product to cart
            $productToAdd = ['quantity' => 1, 'totalSinglePrice' => $price, 'data' => $product];
        }
        $this->items[$id] = $productToAdd;
        $this->totalQuantity++;
        $this->totalPrice = $this->totalPrice + $price;
    }

    public function updatePriceQuantity() {
        $totalPrice = 0;
        $totalQuantity = 0;

        foreach($this->items as $i) {
            $totalQuantity = $totalQuantity + $i['quantity'];
            $totalPrice = $totalPrice + $i['totalSinglePrice'];
        }
        $this->totalQuantity = $totalQuantity;
        $this->totalPrice = $totalPrice;
    }
}