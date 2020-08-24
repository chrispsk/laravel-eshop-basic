@extends('layouts.admin')

@section('body')


<style>
/* The payment window */
.payment-window {
                  display: none; /* Hidden by default */
                  position: fixed; /* Stay in place */
                  z-index: 1; /* Sit on top */
                  padding-top: 100px; /* Location of the box */
                  left: 0;
                  top: 0;
                  width: 100%; /* Full width */
                  height: 100%; /* Full height */
                  overflow: auto; /* Enable scroll if needed */
                  background-color: rgb(0,0,0); /* Fallback color */
                  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
                }

/* payment window content */
.payment-window-content {
                  background-color: #fefefe;
                  margin: auto;
                  padding: 30px;
                  border: 10px solid #888;
                  width: 45%;
                }

.payment-window-close {
                color: #aaaaaa;
                float:right;
                margin-left:20px;
                font-size: 28px;
                font-weight: bold;
}

.payment-window-close:hover,
                .payment-window-close:focus {
                  color: #aaaaaa;
                  text-decoration: none;
                  cursor: pointer;
                }
</style>

<h1>Orders Panel</h1>

@if(session('orderDeletionStatus'))
<div class="alert alert-danger"> {{session('orderDeletionStatus')}} </div>
@endif

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#order_id</th>
            <th>Date</th>
            <th>Delivery Date</th>
            <th>Price</th>
            <th>user_id</th>
            <th>Status</th>
            <th>Edit</th>
            <th>Remove</th>
        </tr>
        </thead>
        <tbody>

        @foreach($orders as $order)  
        <tr>
            <td>{{$order->order_id}}</td>
          
            <td>{{$order->date}}</td>
            <td>{{$order->del_date}}</td>
            <td>{{$order->price}}</td>
            @if(empty($order->name))
            <td>Anonymous</td>
            @else
            <td>{{$order->name}}</td>
            @endif
            
            <td>{{$order->status}}</td>
         
            <td>
    <a class="payment-info-button btn btn-success" 
  onclick="getPaymentInfo('{{$order->order_id}}','{{$order->status}}')"> 
  Payment info 
  </a>
  </td>
         

            <td><a href="{{ route('adminEditOrderForm',['order_id' => $order->order_id ])}}" class="btn btn-primary">Edit</a></td>

            <td><a href="{{ route('adminDeleteOrder',['id' => $order->order_id ])}}"  
                onclick="return confirm('Are you sure you want to delete this order?')"
            class="btn btn-warning">Remove</a></td>


        </tr>

        @endforeach





        </tbody>
    </table>





    
        <!-- The payment window -->
        <div id="my-payment-window" class="payment-window">
        
          <!-- status content -->
          <div class="payment-window-content">
            <span class="payment-window-close">&times;</span>
            <h2>Payment Info</h2>
            <p>Loading..</p>
            <p></p>
            <p></p>
            <p></p>
            <p></p>
         
          </div>
        
        </div>
            



    {{$orders->links()}}

</div>


<script>

function getPaymentInfo(order_id, status) {
    if(status === 'paid') {
        $.get("http://127.0.0.1:8000/payment/getPaymentInfoByOrderId/"+order_id, function(data){
            //var paymentInfo = JSON.parse(data); // if using json_encode
            var paymentInfo = data;
            //alert( "Data Loaded: " + paymentInfo[0].amount );
            $( ".payment-window" ).show();
                          $( ".payment-window-content p:eq(0)" ).text( "ID: " + paymentInfo[0].id);
                          $( ".payment-window-content p:eq(1)" ).text( "Payment ID: " + paymentInfo[0].paypal_payment_id);
                          $( ".payment-window-content p:eq(2)" ).text( "Payer ID: " + paymentInfo[0].paypal_payer_id);
                          $( ".payment-window-content p:eq(3)" ).text( "Amount: $" + paymentInfo[0].amount);
                          $( ".payment-window-content p:eq(4)" ).text( "Date: " + paymentInfo[0].date);
        });
    } else if(status === 'on_hold') {
        $(".payment-window").show();
                $( ".payment-window-content p:eq(0)" ).text( "Not Paid Yet");  
                $( ".payment-window-content p:eq(1)" ).text( "");
                $( ".payment-window-content p:eq(2)" ).text( "");
                $( ".payment-window-content p:eq(3)" ).text( "");
                $( ".payment-window-content p:eq(4)" ).text( "");
    } else {
        $( ".payment-window" ).show();
               $( ".payment-window-content p:eq(0)" ).text( "Undefined status");
                $( ".payment-window-content p:eq(1)" ).text( "");
                $( ".payment-window-content p:eq(2)" ).text( "");
                $( ".payment-window-content p:eq(3)" ).text( "");
                $( ".payment-window-content p:eq(4)" ).text( "");
    }
}

$(".payment-window-close").click(function(){
               $(".payment-window").hide();
        
        });

</script>



@endsection