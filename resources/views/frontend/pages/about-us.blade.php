@extends('frontend.layouts.master')
@section('content')
<section style="background-image: url({{ asset('frontend-assets/img/bg-about-us.jpg') }})" class="bg-img-cover-center py-10 pt-md-16 pb-md-17 bg-overlay">
    <div class="container position-relative z-index-2 text-center">
      <div class="mxw-751">
        <h1 class="text-white fs-30 fs-md-42 lh-15 font-weight-normal mt-4 mb-10" data-animate="fadeInRight">We believe in design as a powerful force for good.</h1> </div>
    </div>
  </section>

  <section class="bg-patten-03 bg-gray-01 pb-5">
    <div class="container">
      <div class="card border-0 mt-n13 z-index-3 mb-2">
        <div class="card-body p-6 px-lg-5 py-lg-5">
          <p class="letter-spacing-263 text-uppercase text-primary mb-6 font-weight-500 text-center">welcome to My BNB Rentals</p>
          <h2 class="text-heading mb-4 fs-22 fs-md-32 text-center lh-16 px-6">We see change as opportunity, not a threat and start with the belief that there is a better way. </h2>
          <p class="text-center px-lg-11 fs-15 lh-17"> Over the past 25 years we’ve created more than 5,000 new homes and 1.5 million sq ft of workspace in over 60 regeneration projects. Have a look at the short film below to learn more about how we’ve achieved this and what drives us. </p>
        </div>
      </div>
    </div>
  </section>

<section class="pt-6">
  <div class="container">
      <h2 class="text-heading mb-4 fs-22 fs-md-32 text-center lh-16 px-md-13"> GrandHome is an estate agency that helps people live in more thoughtful and beautiful ways.</h2>
      <p class="text-center px-md-17 fs-15 lh-17 mb-8"> Our home is at the heart of the design, allowing us to engage with our community through talks and events, and uphold our company culture with film screenings, yoga classes and team lunches. </p>
      <div class="text-center mb-11"> <a href="#" class="btn btn-lg btn-primary">Join our team</a> </div>
      <div class="row galleries mb-lg-n16">
          <div class="col-sm-8 mb-6">
              <div class="item item-size-2-1">
                  <a href="img/gallery-lg-08.jpg" class="card p-0 hover-zoom-in" data-gtf-mfp="true" data-gallery-id="02">
                      <div class="card-img img" style="background-image:url({{ asset('frontend-assets/img/gallery-08.jpg') }})"> </div>
                  </a>
              </div>
          </div>
          <div class="col-sm-4 mb-6">
              <div class="item item-size-2-1">
                  <a href="img/gallery-lg-09.jpg" class="card p-0 hover-zoom-in" data-gtf-mfp="true" data-gallery-id="02">
                      <div class="card-img img" style="background-image:url({{ asset('frontend-assets/img/gallery-09.jpg') }})"> </div>
                  </a>
              </div>
          </div>
          <div class="col-sm-6 mb-6">
              <div class="item item-size-2-1">
                  <a href="img/gallery-lg-10.jpg" class="card p-0 hover-zoom-in" data-gtf-mfp="true" data-gallery-id="02">
                      <div class="card-img img" style="background-image:url({{ asset('frontend-assets/img/gallery-10.jpg') }})"> </div>
                  </a>
              </div>
          </div>
          <div class="col-sm-6 mb-6">
              <div class="item item-size-2-1">
                  <a href="img/gallery-lg-11.jpg" class="card p-0 hover-zoom-in" data-gtf-mfp="true" data-gallery-id="02">
                      <div class="card-img img" style="background-image:url({{ asset('frontend-assets/img/gallery-11.jpg') }})"> </div>
                  </a>
              </div>
          </div>
      </div>
  </div>
</section>

<section class="bg-gray-01 pt-10 pt-lg-12 pb-10" data-animated-id="7">
  <div class="container">
      <h2 class="text-dark lh-1625 text-center mb-8 fs-22 fs-md-32 pt-lg-10">Keep exploring</h2>
      <div class="row">
          <div class="col-sm-6 col-lg-3 mb-6 mb-lg-0">
              <a href="#" class="card border-0 shadow-2 px-7 py-5 h-100 shadow-hover-lg-1">
                  <div class="card-img-top d-flex align-items-end justify-content-center"> <img src="{{ asset('frontend-assets/img/icon-box-4.png') }}" alt="Meet our agents"> </div>
                  <div class="card-body px-0 pt-2 pb-0 text-center">
                      <h4 class="card-title fs-16 lh-186 text-dark hover-primary">Meet our agents</h4> </div>
              </a>
          </div>
          <div class="col-sm-6 col-lg-3 mb-6 mb-lg-0">
              <a href="#" class="card border-0 shadow-2 px-7 py-5 h-100 shadow-hover-lg-1">
                  <div class="card-img-top d-flex align-items-end justify-content-center"> <img src="{{ asset('frontend-assets/img/icon-box-5.png') }}" alt="Sell your home"> </div>
                  <div class="card-body px-0 pt-2 pb-0 text-center">
                      <h4 class="card-title fs-16 lh-186 text-dark hover-primary">Sell your home</h4> </div>
              </a>
          </div>
          <div class="col-sm-6 col-lg-3 mb-6 mb-lg-0">
              <a href="#" class="card border-0 shadow-2 px-7 py-5 h-100 shadow-hover-lg-1">
                  <div class="card-img-top d-flex align-items-end justify-content-center"> <img src="{{ asset('frontend-assets/img/icon-box-6.png') }}" alt="Latest news"> </div>
                  <div class="card-body px-0 pt-2 text-center pb-0">
                      <h4 class="card-title fs-16 lh-186 text-dark hover-primary">Latest news</h4> </div>
              </a>
          </div>
          <div class="col-sm-6 col-lg-3 mb-6 mb-lg-0">
              <a href="#" class="card border-0 shadow-2 px-7 py-5 h-100 shadow-hover-lg-1">
                  <div class="card-img-top d-flex align-items-end justify-content-center"> <img src="{{ asset('frontend-assets/img/icon-box-7.png') }}" alt="Contact us"> </div>
                  <div class="card-body px-0 pt-2 text-center pb-0">
                      <h4 class="card-title fs-16 lh-186 text-dark hover-primary">Contact us</h4> </div>
              </a>
          </div>
      </div>
  </div>
</section>

@endsection