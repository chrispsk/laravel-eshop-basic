<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AdminProductsController extends Controller
{
    public function listing() {
        #$products = Product::all();
        $products = Product::paginate(3);
        return view('admin.displayProducts', ['products' => $products]);
    }

    public function editProductForm($id) { 
        $product = Product::find($id);
        return view('admin.editProductForm', ['product' => $product]);
    }

    public function editProductImageForm($id) {
        $product = Product::find($id);
        return view('admin.editProductImageForm', ['product' => $product]);
    }

    public function updateProduct($id, Request $r) {
        $prod = Product::find($id);
        $name = $r->input("name");
        $description = $r->input("description");
        $type = $r->input("type");
        $price = $r->input("price");

        //handling picture
        $inp = $r->validate([
        'name' => 'required|min:3|max:199',
        'description' => 'required|min:1|max:499',
        'price' => 'required|between:0,999999.99',
        'type' => 'required|min:1|max:7',
        ]);

        $prod->name = $name;
        $prod->description = $description;
        $prod->type = $type;
        $prod->price = $price;
        $prod->save();
        return redirect("admin/products");
    }

    public function updateProductImage($id, Request $r) {
        //handling picture
        $inp = $r->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
            ]);
            if($r->hasFile("image")) {
                $product = Product::find($id);
                $exists = Storage::disk('local')->exists("public/product_images/".$product->image);
                //delete old image
                if($exists) {
                    Storage::delete('public/product_images/'.$product->image);
                }

                //upload new image
            $ext = $r->file('image')->getClientOriginalExtension(); //JPG

            $r->file('image')->storeAs('public/product_images/', $product->image);
            $arrayToUpdate = array('image' => $product->image);
            DB::table('products')->where('id', $id)->update($arrayToUpdate);
            } else {
                dd("No image was selected");
            }
            
        return redirect("admin/products");
    }

    public function deleteProduct($id) {
      $product = Product::find($id);

      $exists =  Storage::disk("local")->exists("public/product_images/".$product->image);

      //if image exists
      if($exists){
          //delete it
          Storage::delete('public/product_images/'.$product->image);
      }

      Product::destroy($id);
        
      return redirect("admin/products");
    }

    public function insert(Request $r) {
        
        return view('admin.insertProducts');
    }

    public function adding(Request $r) {
        $name = $r->input("name");
        $description = $r->input("description");
        $type = $r->input("type");
        $price = $r->input("price");

        //handling picture
        $inp = $r->validate([
        'name' => 'required|min:3|max:199',
        'description' => 'required|min:1|max:499',
        'price' => 'required|between:0,999999.99',
        'type' => 'required|min:1|max:7',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $ext = $r->file('image')->getClientOriginalExtension(); //JPG
        $finalName = str_replace(" ", "", $r->input("name")); // nameOfTheTitle
        $imageName = time() . "-" . $finalName . "." . $ext; //98732498-nameOfTheTitle.jpg
        ######## Method 1 ############
        $r->file('image')->storeAs('public/product_images/', $imageName);
        
        ########## Method 2 ##########
        // $imageEncoded = File::get($r->image); 
        // Storage::disk("local")->put('public/product_images/'.$imageName, $imageEncoded);

        $bag = array('name'=>$name,'description'=>$description, 'image'=>$imageName, 'type'=>$type, 'price'=>$price);
        //this do not creates timestamps
        //I have to do it manually
        //$created = DB::table("products")->insert($bag); 
        $created = Product::create($bag); //this creates timestamps	
        if($created){
            return redirect("/admin/products")->with("ok", "Product added");
        } else {
          return "Not created!";  
        }
        
		
    }
    public function ordersPanel() {
        //$orders = DB::table('orders')->paginate(10);
        $example1 = DB::select("SELECT orders.order_id, orders.status, users.name FROM orders LEFT JOIN users on orders.user_id=users.id");
        $orders = DB::table('orders')
                    ->select('orders.order_id', 'orders.date','orders.del_date','orders.price','orders.user_id','orders.status', 'users.name')
                    ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                    ->paginate(10);
        //dd($orders);
        return view('admin.ordersPanel', ['orders' => $orders]);
    }

    public function adminEditOrderForm($order_id) {
        $order =  DB::table('orders')->where("order_id",$order_id)->get();
        
          return view('admin.editOrderForm',['order'=>$order[0]]);
    }

    public function adminDeleteOrder($id, Request $r) {
        $deleted = DB::table('orders')->where('order_id',$id)->delete();
        if($deleted){
            // delete from the other table as well
            DB::table('order_items')->where('order_id',$id)->delete();
            // flash message
            return redirect()->back()->with('orderDeletionStatus', 'Order '. $id . ' was successfully deleted!');
        } else {
            return redirect()->back()->with('orderDeletionStatus', 'Order '. $id . ' was not deleted!');
        }
    }

    public function getPaymentInfoByOrderId($order_id, Request $r){
        $info = DB::table('payments')->where('order_id', $order_id)->get();
        
        //return json_encode($info);
        return response()->json($info);
    }

    public function updateOrder(Request $request,$order_id) {
        $date =  $request->input('date');
       $del_date =  $request->input('del_date');
       $status = $request->input('status');
       $price = $request->input('price');

       $updateArray = array("date"=>$date, "del_date"=> $del_date,"status"=>$status,"price"=>$price);

        DB::table('orders')->where('order_id',$order_id)->update($updateArray);

        return redirect()->route("ordersPanel");
    }

    public function ajaxform() {

        return view("testajax");
    }

    public function ajaxpost(Request $r) {
        if($r->ajax()){
            $a = $r->all();
        // use $a to make queries into database
        //return response()->json($a,200) ;
        //return $a;
        return response()->json(['success' => 'Form data is successfully stored']);
        }
        
    }
}
