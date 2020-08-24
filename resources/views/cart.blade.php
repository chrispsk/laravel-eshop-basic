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
			<div class="table-responsive cart_info">
				<table class="table table-condensed">
					<thead>
						<tr class="cart_menu">
							<td class="image">Item</td>
							<td class="description"></td>
							<td class="price">Price</td>
							<td class="quantity">Quantity</td>
							<td class="total">Total</td>
							<td></td>
						</tr>
					</thead>
					<tbody>
                    @foreach($cartItems->items as $i)

                    <tr>
							<td class="cart_product">
								<a href=""><img src="{{Storage::disk('local')->url('product_images/'.$i['data']['image'])}}" alt="" width="100" height="100"></a>
							</td>
							<td class="cart_description">
								<h4><a href="">{{ $i['data']['name'] }}</a></h4>
                                
                                <p>ID: {{ $i['data']['id'] }}</p>
							</td>
							<td class="cart_price">
								<p>{{ $i['data']['price'] }}</p>
							</td>
							<td class="cart_quantity">
								<div class="cart_quantity_button">
									<a class="cart_quantity_up" href="{{route('increase', ['id' => $i['data']['id']])}}"> + </a>
									<input class="cart_quantity_input" type="text" name="quantity" value="{{ $i['quantity'] }}" autocomplete="off" size="2">
									<a class="cart_quantity_down" href="{{route('decrease', ['id' => $i['data']['id']])}}"> - </a>
								</div>
							</td>
							<td class="cart_total">
								<p class="cart_total_price">${{ $i['totalSinglePrice'] }}</p>
							</td>
							<td class="cart_delete">
								<a class="cart_quantity_delete" href="{{ route('deleteItem', ['id' => $i['data']['id']]) }}"><i class="fa fa-times"></i></a>
							</td>
						</tr>

@endforeach
						

					</tbody>
				</table>
			</div>
		</div>
	</section> <!--/#cart_items-->

	<section id="do_action">
		<div class="container">
			<div class="heading">
				<h3>What would you like to do next?</h3>
				<p>Choose if you have a discount code or reward points you want to use or would like to estimate your delivery cost.</p>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="chose_area">
						<ul class="user_option">
							<li>
								<input type="checkbox">
								<label>Use Coupon Code</label>
							</li>
							<li>
								<input type="checkbox">
								<label>Use Gift Voucher</label>
							</li>
							<li>
								<input type="checkbox">
								<label>Estimate Shipping & Taxes</label>
							</li>
						</ul>
						<ul class="user_info">
							<li class="single_field">
								<label>Country:</label>
								<select>
									<option>United States</option>
									<option>Bangladesh</option>
									<option>UK</option>
									<option>India</option>
									<option>Pakistan</option>
									<option>Ucrane</option>
									<option>Canada</option>
									<option>Dubai</option>
								</select>
								
							</li>
							<li class="single_field">
								<label>Region / State:</label>
								<select>
									<option>Select</option>
									<option>Dhaka</option>
									<option>London</option>
									<option>Dillih</option>
									<option>Lahore</option>
									<option>Alaska</option>
									<option>Canada</option>
									<option>Dubai</option>
								</select>
							
							</li>
							<li class="single_field zip-field">
								<label>Zip Code:</label>
								<input type="text">
							</li>
						</ul>
						<a class="btn btn-default update" href="">Get Quotes</a>
						<a class="btn btn-default check_out" href="">Continue</a>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="total_area">
						<ul>
							<li>Quantity <span>{{$cartItems->totalQuantity}}</span></li>
							<li>Shipping Cost <span>Free</span></li>
							<li>Total <span>${{$cartItems->totalPrice}}</span></li>
						</ul>
							<a class="btn btn-default update" href="">Update</a>
							<!-- createOrder -->
							<a class="btn btn-default check_out" href="{{route('checkoutProducts')}}">Check Out</a>
					</div>
				</div>
			</div>
		</div>
    </section><!--/#do_action-->
    
@endsection