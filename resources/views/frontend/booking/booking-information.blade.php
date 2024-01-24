@php
    $toDay = date('Y-m-d');
    $date1 = new DateTime($toDay);
    $date2 = new DateTime($bookingInformation->check_in);
    $interval = $date1->diff($date2);
    $diffDays = $interval->days;
@endphp
@extends('frontend.layouts.master')
@push('css')
    <link rel="stylesheet" href="{{ asset('frontend-assets/css/travel.css') }}">
@endpush
@section('content')
    <main id="content">
        <section class="pb-4 page-title shadow">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb pt-6 lh-15 pb-3">
                        {{-- <li class="breadcrumb-item"><a href="#">< Back</a></li> --}}
                    </ol>
                    <h1 class="fs-30 lh-1 mb-0 text-heading font-weight-600">Request to book</h1>
                </nav>
            </div>
        </section>
        <section class="pt-8 pb-11 bg-gray-01">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mb-8 mb-lg-0 order-1 order-lg-2">
                        <div class="py-5 px-4 border rounded-lg shadow-hover-1 bg-white mb-4" data-animate="fadeInUp">
                            <div class="propertypayment">
                                <h2>Your trip</h2>
                                <ul>
                                    <li>
                                        <strong>Dates</strong>
                                        <span>{{ date('jS M', strtotime($bookingInformation->check_in)) }} –
                                            {{ date('jS M Y', strtotime($bookingInformation->check_out)) }}</span>
                                    </li>
                                    <li>
                                        <strong>Adults</strong>
                                        <span>{{ $bookingInformation->total_guest }} guests</span>
                                    </li>
                                    <li>
                                        <strong>Children</strong>
                                        <span>{{ $bookingInformation->total_children }} children</span>
                                    </li>
                                </ul>
                                <h2>Choose how to pay</h2>
                                <div class="pricebox">
                                    <div class="priceboxone">
                                        <div class="row d-flex align-items-center justify-content-center">
                                            <div class="col-lg-8">
                                                <strong>Pay in full</strong>
                                                <span>Pay the total (${{ number_format($bookingInformation->total_amount, 2) }}).</span>
                                            </div>
                                            <div class="col-lg-4 text-center">
                                                <input type="radio" name="payment_type" value="full" checked>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                         $halfPayment = 0;
                                    @endphp
                                    @if ($diffDays >= 30)
                                        <div class="priceboxone border-0">
                                            <div class="row d-flex align-items-center justify-content-center">
                                                <div class="col-lg-8">
                                                    <strong>Pay part now, part later</strong>
                                                    @php
                                                        $halfPayment = ($bookingInformation->total_amount * 50) / 100;
                                                    @endphp
                                                    <span>${{ number_format($halfPayment??0, 2) }} due today,${{ number_format($bookingInformation->total_amount - $halfPayment??0, 2) }} on {{ date('jS M Y', strtotime('-30 days', strtotime($bookingInformation->check_in))) }}. No extra fees.</span>
                                                </div>
                                                <div class="col-lg-4 text-center">
                                                    <input type="radio" name="payment_type" value="partial">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <hr>
                                <h2>Pay with</h2>
                                <form id="makePayment">
                                    <div class="form-group">
                                        <label for="key-word" class="sr-only">Card Number</label>
                                        <input type="text" class="form-control form-control-lg border-0 shadow-none" id="card-number" name="card_number" placeholder="Card Number...">
                                        <span class="card_number text-danger"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label for="key-word" class="sr-only">Expiry</label>
                                            <input type="text" class="form-control form-control-lg border-0 shadow-none" name="expiry_month" placeholder="MM/YY">
                                            <span class="expiry_month text-danger"></span>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label for="key-word" class="sr-only">CVV</label>
                                            <input type="text" class="form-control form-control-lg border-0 shadow-none" name="cvv_pin" placeholder="CVV">
                                            <span class="cvv_pin text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="key-word" class="sr-only">Card Holder Name</label>
                                        <input type="text" class="form-control form-control-lg border-0 shadow-none" id="key-word" name="card_holder_name" placeholder="Card Holder Name">
                                        <span class="cvv_pin text-danger"></span>
                                    </div>
                                    <h2>Cancellation policy</h2>
                                    <p>{{ $bookingInformation->property->cancelletionPolicies->description }}</p>
                                    <p>{{ $bookingInformation->property->cancelletionPolicies->note }}</p>
                                    <button type="submit" class="btn btn-primary btn-lg btn-block shadow-none mt-4">Confirm and pay</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 order-2 order-lg-2 primary-sidebar sidebar-sticky" id="sidebar">
                        <div class="primary-sidebar-inner">
                            <div class="card mb-4">
                                <div class="card-body px-6 py-4">
                                    <div class="row d-flex align-items-center justify-content-center">
                                        <div class="col-md-6">
											<img src="{{ url('public/storage/upload/property_image/main_image/' . $bookingInformation->property->property_main_photos) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="property-detail">
                                                {{-- <small>Room in nature lodge</small> --}}
                                                <strong>{{ $bookingInformation->property->property_name }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h2>Price details</h2>
                                    @php
                                        $bookingDetails = json_decode($bookingInformation->booking_summary, true);
                                        ksort($bookingDetails, 5);
                                    @endphp
                                    @foreach ($bookingDetails as $key => $booking)
                                        <div class="row d-flex align-items-center justify-content-end">
                                            <div class="col-lg-6">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                            <div class="col-lg-6 text-right">${{ number_format($booking, 2) }}</div>
                                        </div>
                                    @endforeach
                                    <div class="due-price-section d-none">
                                        <hr>
                                        <div class="row d-flex align-items-center justify-content-end">
                                            <div class="col-lg-6"><strong>Due now</strong></div>
                                            <div class="col-lg-6 text-right"><strong>${{ number_format($halfPayment??0, 2) }} </strong></div>
                                        </div>
                                        <div class="row d-flex align-items-center justify-content-end">
                                            <div class="col-lg-6">{{ date('jS M Y', strtotime('-30 days', strtotime($bookingInformation->check_in))) }}(Next Payment Date)</div>
                                            <div class="col-lg-6 text-right">${{ number_format($bookingInformation->total_amount - $halfPayment??0, 2) }} </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
@push('js')
<script>
    "use strict"
    $("input[name=payment_type]").on("click",function(){
       if($(this).val()=='partial'){
            $(".due-price-section").removeClass("d-none");
       }else{
            $(".due-price-section").addClass("d-none");
       }
    });

    makePayment.onsubmit = async(e)=>{
        showLoader();
        e.preventDefault();
        var formData = new FormData(makePayment);
        formData.append("payment_type",$("input[name=payment_type]:checked").val());
        const response = await fetch('{{route("make.paymnet")}}',{
            method:"POST",
            body:formData ,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        console.log(response.status);
        const result = await response.json();
        if(response.status==403){
            hideLoader();
            toastr.error(response.statusText);
        }
        if(result.status==500){
            hideLoader();
            toastr.error(result.msg);
            return false;
        }
        if(response.status==401){
            hideLoader();
        }
        if(response.status==500 && result.status) {
            hideLoader();
            toastr.error(response.statusText);
        }

        if(response.status==422){
            hideLoader();
            $("#makePayment").find("span").text('');
            for(let index in result.errors){
                $("."+index).text(result.errors[index]);
            }
        }
        if(response.status ==200){
            // toastr.success()
            window.setTimeout(() => {
                hideLoader();
                window.location.href = result.url; 
            }, 2000);
        }
        if(result.status==false){
            // toastr.success()
            window.setTimeout(() => {
                hideLoader();
                window.location.href = result.url; 
            }, 2000);
        }

        // if(!result.status && response.status !=401){
        //     hideLoader();
        //     toastr.error(result.msg);
        //     window.setTimeout(() => {
        //         window.location.reload(); 
        //     }, 2000);
        // }
    }
</script>
    
@endpush
