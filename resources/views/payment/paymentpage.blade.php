
@extends('layouts.main')

@section('center')
<section id="cart_items">
    <div class="container">
        <div class="breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li class="active">Shopping Cart</li>
            </ol>
        </div>
       
            <div class="shopper-informations">
                <div class="row">
            
                    <div class="col-sm-12 clearfix">
                        <div class="bill-to">
                            <p> Shipping/Bill To</p>
                            <div class="form-one">
                                
                                          
            <div class="total_area">
                    <ul>
                    <li>Payment Status 
                    @if($payment_info['status'] == 'on_hold')
                    <span>Not paid yet!</span>
                    @endif
                    
                    </li>
                        <li>Shipping Cost <span>Free</span></li>
                        <li>Total <span>{{$payment_info['price']}}</span></li>
                    </ul>
                    <a class="btn btn-default update" href="">Update</a>
                    <a class="btn btn-default check_out" id="paypal-button">Pay Now</a>
                </div>
                                          
                                          
                                          
                                          
                            </div>
                            <div class="form-two">
                                
                            </div>
                        </div>
                    </div>
                           
                </div>
            </div>
            
       
       
    </div>
</section> <!--/#payment-->

@endsection






<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
  paypal.Button.render({
    // Configure environment
    env: 'sandbox', //for live add 'production'
    client: {
      sandbox: 'AfgbMSQrfT_b0yy3bEL4jAMJ70rtjEbmdkE8rRIp_WIRd7nuQD4h9fh2trI1xWi9WCGcDSDLAcGFLwqg',
      production: 'demo_production_client_id' // for LIVE (add Client_id token)
    },
    // Customize button (optional)
    locale: 'en_US',
    style: {
      size: 'small',
      color: 'gold',
      shape: 'pill',
    },

    // Enable Pay Now checkout flow (optional)
    commit: true,

    // Set up a payment
    payment: function(data, actions) {
      return actions.payment.create({
        transactions: [{
          amount: {
            total: "{{$payment_info['price']}}", //pass it as a string
            currency: 'USD'
          }
        }]
      });
    },
    // Execute the payment
    onAuthorize: function(data, actions) {
      return actions.payment.execute().then(function() {
        // Show a confirmation message to the buyer
        window.alert('Thank you for your purchase!');
        // OUR CODE... redirect the user to a page (receipt)
        //window.location = './paymentreceipt/'+data.paymentID+'/'+data.payerID;
        window.location = "{{url('payment/paymentreceipt')}}"+"/"+data.paymentID+'/'+data.payerID;
      });
    }
  }, '#paypal-button');

</script>