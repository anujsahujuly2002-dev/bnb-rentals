@extends('frontend.layouts.master')
@section('content')
<main id="content">
    <section class="pt-2 pb-4 page-title shadow">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pt-6 pt-lg-2 pb-2 lh-15">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">List Your Property</li>
                </ol>
            </nav>
            <h1 class="fs-30 lh-16 mb-1 text-dark font-weight-600">Sign Up In 60 sec.</h1>
        </div>
    </section>
    <section class="pt-8 pb-8" data-animated-id="2">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="border-bottom pb-4">
                        <h2 class="text-heading mb-2 fs-40 lh-15 pr-6">My BNB Rentals</h2>
                        <h5>How It Works</h5>
                        <ul class="listingstyle">
                            <li>1: Register as an MyBNBRentals.com member</li>
                            <li>2: List your property and add your photos</li>
                            <li>3: Take bookings and generate income</li>
                        </ul>

                        <hr>

                        <h5 class="mb-3">Why join MyBNBRentals.com?</h5>
                        <strong>Annual Subscription</strong>
                        <p>There's no risk and no obligation</p>

                        <strong>Fewer Listings = More Exposure:</strong>
                        <p>Tired of listing on sites next to 100,000 other properties? Join the club. They may have
                            bigger marketing budgets, but what does it matter if nobody can find you?</p>

                        <strong>Lower Rates:</strong>
                        <p>A basic listing costs only $399 per year. There are no commissions and other hidden fees.
                            Even one extra rental will easily cover the cost.</p>


                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#exampleModal">View Features</button>

                    </div>
                </div>
                <div class="col-lg-5 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="card border-0 widget-request-tour">
                            <div class="card-body px-sm-6 shadow-xxs-2 pb-5 pt-5">
                                <div class="mainformheading">Sign up now !</div>
                                <form id="owner-register-forms" >
                                    @csrf
                                    <div class="form-group mb-2">
                                        <input class="form-control form-control-lg border-0" placeholder="First Name, Last Name" name="full_name">
                                        <span class="full_name_error text-danger"></span>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="email" class="form-control form-control-lg border-0"
                                            placeholder="Your Email" name="username">
                                            <span class="username_error text-danger"></span>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="text" class="form-control form-control-lg border-0"
                                            placeholder="Your phone" name="phone">
                                            <span class="phone_error text-danger"></span>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="password" class="form-control form-control-lg border-0"
                                            placeholder="Password" name="password">
                                            <span class="password_error text-danger"></span>  
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="password" class="form-control form-control-lg border-0"
                                            placeholder="Confirm Password" name="cnf_password">
                                            <span class="cnf_password_error text-danger"></span>  
                                    </div>
                                    <div class="form-group form-check mt-2 mb-4">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1" value="1"
                                            name="verify">
                                        <label class="form-check-label fs-13" for="exampleCheck1">I agree with terms and
                                            conditions</label>
                                        </div>
                                        <span class="verify_error text-danger"></span> 
                                    <button type="submit"
                                        class="btn btn-primary btn-lg btn-block rounded">Continue</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-gray-01 pt-8 pb-8">
        <div class="container">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-lg-6"><img src="{{ asset('frontend-assets/img/list-1.jpg') }}" alt=""></div>
                <div class="col-lg-6">
                    <div class="textstyle">
                        <strong>Last Minute Deals:</strong>
                        <p>A hugely popular section of our site. Why let your property sit vacant for those fast
                            approaching weeks? Everyone loves a bargain, so put some heads in your bed</p>
                        <hr>
                        <strong>Links:</strong>
                        <p>We know that you can sell your property better than we can, so we provide your listing with a
                            direct link to your personal web site.</p>
                        <hr>
                        <strong>Unlimited Photos:</strong>
                        <p>Why do some sites still limit the number of photos you can add? We don't know.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-color-01 pt-8 pb-8">
        <div class="container">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-lg-6">
                    <div class="textstyle textstylewhite">
                        <strong>Calendar Sync:</strong>
                        <p>No need to update multiple sites - we can sync your availability with any external calendar.
                        </p>
                        <hr>
                        <strong>No Booking or Service Fees:</strong>
                        <p>Travelers are more likely to use our site (and pay your rental rate) because we don't charge
                            them any additional fees.</p>
                        <hr>
                        <strong>Back to the Future:</strong>
                        <p>We don't hold onto your money, don't block traveler contact information, don't force you to
                            use our cancellation policies, and don't charge a booking or service fee. We put you, the
                            property owner, back in control. We're the vacation rental listing site you used to love!
                        </p>
                    </div>
                </div>
                <div class="col-lg-6"><img src="{{ asset('frontend-assets/img/list-2.jpg') }}" alt=""></div>
            </div>
        </div>
    </section>

    <section class="listsectionprocess pt-8 pb-8">
        <div class="container">
            <h2>List Your Vacation Rental Property</h2>
            <div class="row">
                <div class="col-lg-4">
                    <div class="listBox">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <h3>Annual Subscription</h3>
                        <p>Pay by credit card or PayPal</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="listBox">
                        <i class="fa fa-laptop" aria-hidden="true"></i>
                        <h3>No Setup</h3>
                        <p>We’ll create your listing for you</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="listBox">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <h3>Mass Exposure</h3>
                        <p>Access to millions of travelers looking for a place to stay</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
@push('js')
<script>
    $("#owner-register-forms").on("submit",function(e){
    e.preventDefault();
    showLoader();
    $.ajax({
        url:site_url+"/auth/owner-register",
        type: "POST",
        data:new FormData(this),
        contentType: false,
        cache: false,
        processData:false,
        success:function(res){
            hideLoader();
            console.log(res);
            if(res.status=='1'){
                toastr.success(res.msg);
                window.setTimeout(() => {
                    window.location.href=res.url; 
                 }, 2000);

            }else{
                toastr.error(res.msg);
            }
        },error: function(xhr, ajaxOptions, thrownError){
            hideLoader();
            $(".full_name_error").text("");
            $(".username_error").text("");
            $(".phone_error").text("");
            $(".password_error").text("");
            $(".cnf_password_error").text("");
            $(".verify_error").text("");
            let error = xhr.responseJSON.errors;
            $(".full_name_error").text(error.full_name);
            $(".username_error").text(error.username);
            $(".phone_error").text(error.phone);
            $(".password_error").text(error.password);
            $(".cnf_password_error").text(error.cnf_password);
            $(".verify_error").text(error.verify);
            $(".verify_error").text(error.phone);
        }
    })
})
</script>

@endpush