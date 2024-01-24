@extends('admin.layouts.master')
@section('content')
    <main id="content" class="bg-gray-01">
        <div class="content-body">
            
            <div class="row page-titles mx-0">
                <div class="col p-md-0">
                    <div class="row">
                        <div class="col-md-6"><h4 style="color:black">Booking Information</h4></div>                             
                    </div>
                </div>
            </div>
            
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body ownerfullcontainer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="ownerlistingpage">
                                            <small>Traveller Name</small>
                                            <h1>{{$bookingDetails->user->name}}</h1>
                                            <strong>{{$bookingDetails->user?->phone}}</strong>
                                            {{-- <span>{{$bookingDetails?->property?->address}}</span> --}}
                                            {{-- <a href="javascript:void(0)">Send {{$bookingDetails?->user?->name}} a Message</a> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="ownerlistingpage">
                                            <small>Owner Name</small>
                                            <h1>{{$bookingDetails->property->user->name}}</h1>
                                            <strong>{{$bookingDetails->property->user?->phone}}</strong>
                                            {{-- <span>{{$bookingDetails?->property?->address}}</span> --}}
                                            {{-- <a href="javascript:void(0)">Send {{$bookingDetails?->user?->name}} a Message</a> --}}
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="ownerpropertyname">
                                    <h2>{{$bookingDetails?->property?->property_name}}</h2>
                                    <span>Property Id ({{$bookingDetails->property_id}})</span>
                                </div> 
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="ownerbookingdate border-right">
                                            <span>Check-in</span>
                                            <h2>{{date('D ,M d,Y',strtotime($bookingDetails->check_in))}}</h2>
                                            <small>{{$bookingDetails?->property?->check_in}}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="ownerbookingdate">
                                            <span>Checkout</span>
                                            <h2>{{date('D ,M d,Y',strtotime($bookingDetails->check_out))}}</h2>
                                            <small>{{$bookingDetails?->property?->check_out}}</small>
                                        </div>
                                    </div>
                                </div>
                                <hr>
            					<div class="row">
            						<div class="col-md-4">
            							<div class="ownerguest border-right">
            								<h2>Adults</h2>
            								<p>{{$bookingDetails->total_guest}} adults</p>
            							</div>
            						</div>
            						<div class="col-md-4">
            							<div class="ownerguest border-right">
            								<h2>Children</h2>
            								<p>{{$bookingDetails->total_children}} children</p>
            							</div>
            						</div>
            						<div class="col-md-4">
            							<div class="ownerguest">
            								<h2>Total Guest</h2>
            								<p>{{$bookingDetails->total_guest + $bookingDetails->total_children}} guest</p>
            							</div>
            						</div>
            					</div>
                                
                                <hr>
                                {{-- <div class="ownermoredetail">
                                    <h2>Confirmation code</h2>
                                    <p>HMWMWS2QPW</p>
                                </div>
                                <hr> --}}
            					@php
            						$bookingDetailss = json_decode($bookingDetails->booking_summary, true);
            						ksort($bookingDetailss, 5);
            					@endphp
                                <div class="amountpaid">
                                    <h2>Guest paid</h2>
                                    <div class="responsive mt-2">
                                        <table class="table table-bordered">
                                            <tbody>
            									@foreach ($bookingDetailss as $key=> $item)
            										<tr>
            											@if ($key =='total_amount')
            												<td><h3>Total Booking Amount (USD)</h3></td>
            												<td align="right"><h3>${{ number_format($item, 2) }}</h3></td>
            											@else
            												<td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
            												<td align="right">${{ number_format($item, 2) }}</td>
            											@endif
            										</tr>
            									@endforeach
            									<tr>
            									<td>Guest Paid</td>
            									<td align="right">${{$bookingDetails->total_amount - $bookingDetails->dues_amount}}</td>
            									</tr>
            									@if ($bookingDetails->dues_amount !=0)
            										<tr>
            											<td>Remaining Balance <small><i>Next payment due : {{(date('d-m-Y',strtotime($bookingDetails->next_payment_date)))}}</i></small></td>
            											<td align="right" class="redcolor">${{$bookingDetails->dues_amount}}</td>
            										</tr>
            									@endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br><hr><br>
                                <div class="amountpaid">
                                    <h2>Host payout</h2>
                                    @php
            						$bookingDetailss = json_decode($bookingDetails->booking_summary, true);
            						ksort($bookingDetailss, 5);
            					    @endphp
                                    <div class="responsive mt-2">
                                       <table class="table">
                                         <tbody>
                                            @php
                                                $total_amount = 0;
                                            @endphp
                                            @foreach ($bookingDetailss as $key => $item)
                                                @if(!in_array($key,['refundable_damage_deposite','tax','total_amount']))
                                                    @php
                                                        $total_amount += $item;        
                                                    @endphp
                                                    <tr>
                                                        <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                        <td align="right">${{ number_format($item, 2) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                {{-- @dd($total_amount); --}}
                                                <td>Service fee</td>
                                                <td align="right">-${{$total_amount*8/100}}</td>
                                           </tr>
                                           <tr>
                                                <td><h3>Total (USD)</h3></td>
                                                <td align="right"><h3>${{$total_amount-($total_amount*8/100)}}</h3></td>
                                           </tr>
                                         </tbody>
                                       </table>                                    
                                    </div>
                                 </div>
                                <hr>
                                <div class="ownermoredetail">
                                    <h2>Cancellations Policy</h2>
                                    <p>{{$bookingDetails?->cancelletionPolicies?->name}}</p>
                                    <p>{{$bookingDetails?->cancelletionPolicies?->description}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
        </div>
      </main>
@endsection
