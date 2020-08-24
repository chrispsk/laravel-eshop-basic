<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\Cart;
use App\Mail\TestMail;
use App\Jobs\SendReminderEmail;
use Mail;

class ProductsController extends Controller
{
    public function me()
    {   
        return response()->json(['das'=>'cool']);
    }

    public function laView1(Request $r) {
        // $products = [
        //     0 => ['name' => 'Phone'],
        //     1 => ['name' => 'Laptop'],
        //     2 => ['name' => 'Fruits']
        // ];

        // $products = DB::table('products')->get();
        // $r->session()->forget('cart');
        // $r->session()->flush();
        
        ###########################################
        //sending mail test
        
        // $data = array('name'=>'Chris', 'content'=>'Ce plm?');
        // Mail::to('boom@gmail.com')->send(new TestMail($data));
        // echo("Email was sent!");
        
        //$when = now()->addMinutes(10);
        //Mail::to('a@b.com')->queue(new TestMail("Ce plm?"));
        //Mail::to('a@b.com')->later($when, new TestMail("Ce plm baaa?"));
        // $data = array('name'=>"John Travolta", 'content'=>'Hello! How are you?');
        // Mail::send('mail', $data, function($message) {
        //     $message->to('abc@gmail.com')->subject('Laravel Basic Testing Mail');
        //     $message->from('hello@example.com','E-Shop Laravel');
        //  });
        //echo("Basic Email Sent. Check your inbox.");
         ##############################################
        ################# USE QUEUE EMAIL ###############
        
        // dispatch(new SendReminderEmail());
        
        ################################################
        $products = Product::paginate(3);

        return view('allproducts', compact('products'));
    }

    public function search(Request $r){
        $searchText = $r->get('searchText');
        $products = Product::where('name', 'Like', $searchText.'%')->paginate(3);
        return view('allproducts', compact('products'));
    }

    public function men(Request $r) {
        $products = Product::where('type', 'Male')->get();
        return view('menProduct', compact('products'));
    }

    public function women(Request $r) {
        $products = DB::table('products')->where('type', "Women")->get();
        return view('womenProduct', compact('products'));
    }

   public function addtocart($id, Request $r){
        $prevCart = $r->session()->get('cart');
        $cart = new Cart($prevCart);

        $product = Product::find($id);
        $cart->addItem($id, $product);
        $r->session()->put('cart', $cart);

        //dump($cart);

        return back();
    }

    public function showCart(Request $r) {
        $cart = $r->session()->get('cart');
        
        if($cart) { //cart not empty
            //dump($cart);        
            return view('cart', ['cartItems' => $cart]);
        } else { //cart is empty
            return redirect("/");
        }
        
    }

    public function deleteItem($id, Request $r) {
        $cart = $r->session()->get("cart");
        if(array_key_exists($id, $cart->items)) {
            unset($cart->items[$id]);
        }

        $prevCart = $r->session()->get("cart");
        $updatedCart = new Cart($prevCart);
        $updatedCart->updatePriceQuantity();

        $r->session()->put('cart', $updatedCart);

        return back();
    }

    public function increaseProduct($id, Request $r){
        $prevCart = $r->session()->get('cart');
        $cart = new Cart($prevCart);

        $product = Product::find($id);
        $cart->addItem($id, $product); // this increase or decrease the quantity
        $r->session()->put('cart', $cart);
        return back();
    }

    public function decreaseProduct($id, Request $r){
        $prevCart = $r->session()->get('cart');
        $cart = new Cart($prevCart);
        if($cart->items[$id]['quantity'] > 1){
            $product = Product::find($id);
            $cart->items[$id]['quantity'] = $cart->items[$id]['quantity']-1;
            $cart->items[$id]['totalSinglePrice'] = $cart->items[$id]['quantity'] * $product['price'];
            $cart->updatePriceQuantity();
            $r->session()->put('cart', $cart);
        }
        return back();
    }

    

    public function createNewOrder(Request $r) {
        ######## COLLECT DATA ########
        $cart = $r->session()->get('cart');
        
       $first_name = $r->input('first_name');
       $address = $r->input('address');
       $last_name = $r->input('last_name');
       $zip = $r->input('zip');
       $phone = $r->input('phone');
       $email = $r->input('email');

    //check if user is logged in or not
    $isUserLoggedIn = Auth::check();

      if($isUserLoggedIn){
      	//get user id
         $user_id = Auth::id();  //OR $user_id = Auth:user()->id;

      }else{
      	//user is guest (not logged in OR Does not have account)
        $user_id = 0;
      }
      #################### END COLLECTING DATA ######################################
        //cart is not empty
        if($cart) {
        // dump($cart);
            $date = date('Y-m-d H:i:s');
            $newOrderArray = array("status"=>"on_hold","date"=>$date,"del_date"=>$date,"price"=>$cart->totalPrice,
            "first_name"=>$first_name, "address"=> $address, 'last_name'=>$last_name, 'zip'=>$zip,'email'=>$email,'phone'=>$phone, 'user_id' => $user_id);
            
            $created_order = DB::table("orders")->insert($newOrderArray);
            $order_id = DB::getPdo()->lastInsertId();


            foreach ($cart->items as $cart_item){
                $item_id = $cart_item['data']['id'];
                $item_name = $cart_item['data']['name'];
                $item_price = $cart_item['data']['price'];
                $newItemsInCurrentOrder = array("item_id"=>$item_id,"order_id"=>$order_id,"item_name"=>$item_name,"item_price"=>$item_price);
                $created_order_items = DB::table("order_items")->insert($newItemsInCurrentOrder);
            }


            //send the email
            $data = array('name'=>'Chris', 'content'=>'Ce plm?');
            Mail::to($email)->send(new TestMail($data)); //$cart
            
            //delete cart
            $r->session()->forget("cart");
            
            //put the order in the session in order to use it next page
            $payment_info =  $newOrderArray;
            $payment_info['order_id'] = $order_id;
            $r->session()->put('payment_info',$payment_info);

        //print_r($newOrderArray);
            
        return redirect()->route("showPaymentPage");

        }else{

          return redirect("/");

     
        }



    }

    public function checkoutProducts() {

        return view('checkoutProducts');
    }

    public function showPaymentReceipt($paypalPaymentID, $paypalPayerID, Request $r) {
        if(!empty($paypalPaymentID) && !empty($paypalPayerID)){
            // will return json -> contains transaction status
            $this->validate_payment($paypalPaymentID, $paypalPayerID);
            $this->storePaymentInfo($paypalPaymentID, $paypalPayerID, $r);
            $payment_receipt = $r->session()->get('payment_info');
            $payment_receipt['paypal_payment_id'] = $paypalPaymentID;
            $payment_receipt['paypal_payer_id'] = $paypalPayerID;

            $r->session()->forget("payment_info");

            return view('payment.paymentreceipt', ['payment_receipt' => $payment_receipt]);

        } else {
            return redirect("/");
        }
    }

    private function storePaymentInfo($paypalPaymentID,$paypalPayerID, Request $r){
        $payment_info = $r->session()->get('payment_info');
        $order_id = $payment_info['order_id'];
        $status = $payment_info['status'];
        $paypal_payment_id = $paypalPaymentID;
        $paypal_payer_id = $paypalPayerID;


    if($status == 'on_hold'){
    
     //create (issue) a new payment row in payments table
         $date = date('Y-m-d H:i:s');
         $newPaymentArray = array("order_id"=>$order_id,"date"=>$date,"amount"=>$payment_info['price'],
             "paypal_payment_id"=>$paypal_payment_id, "paypal_payer_id" => $paypal_payer_id);

         $created_order = DB::table("payments")->insert($newPaymentArray);
        

    //update payment status in orders table to "paid"
    
    DB::table('orders')->where('order_id', $order_id)->update(['status' => 'paid']);
    
   }



 }

    //check if payment was approved
    //may not work from localhost
    private function validate_payment($paypalPaymentID, $paypalPayerID){

        $paypalEnv       = 'sandbox'; // Or 'production'
        $paypalURL       = 'https://api.sandbox.paypal.com/v1/'; //change this to paypal live url when you go live
        $paypalClientID  = 'AfgbMSQrfT_b0yy3bEL4jAMJ70rtjEbmdkE8rRIp_WIRd7nuQD4h9fh2trI1xWi9WCGcDSDLAcGFLwqg';
        $paypalSecret   = 'EOhUg1AntE4mXcdnhBLVsaed-B04a2CYOimIJ37V7-AhtYdJKiizgLFtWSQFuh74Dx27Xohy2Z9FZx7n';
       
   
   
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $paypalURL.'oauth2/token');
           curl_setopt($ch, CURLOPT_HEADER, false);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_USERPWD, $paypalClientID.":".$paypalSecret);
           curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
           $response = curl_exec($ch);
           curl_close($ch);
           
           if(empty($response)){
               return false;
           }else{
               $jsonData = json_decode($response);
               $curl = curl_init($paypalURL.'payments/payment/'.$paypalPaymentID);
               curl_setopt($curl, CURLOPT_POST, false);
               curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
               curl_setopt($curl, CURLOPT_HEADER, false);
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
               curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                   'Authorization: Bearer ' . $jsonData->access_token,
                   'Accept: application/json',
                   'Content-Type: application/xml'
               ));
               $response = curl_exec($curl);
               curl_close($curl);
               
               // Transaction data
               $result = json_decode($response);
               
               return $result;
           }
       
       }

    public function showPaymentPage(Request $r) {
        $payment_info = $r->session()->get("payment_info"); 
        //print_r($payment_info);
        //has not payed yet
        if($payment_info['status'] == 'on_hold'){
            return view('payment.paymentpage', ['payment_info'=>$payment_info]);
        }
        return view('/');
    }

     
}
