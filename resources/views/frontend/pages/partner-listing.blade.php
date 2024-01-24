@extends('frontend.layouts.master')
@section('content')
   <main id="content">
      	<section class="pb-5 page-title shadow">
			<div class="container">
				<nav aria-label="breadcrumb">
					<h1 class="fs-30 lh-1 mt-5 mb-0 text-heading font-weight-600">Partners Listing</h1>
				</nav>
			</div>
      	</section>
      	<section class="pt-8 pb-11 bg-gray-01">
         	<div class="container">
				<div class="row">
					<div class="col-lg-4 order-2 order-lg-1 primary-sidebar sidebar-sticky" id="sidebar">
						<div class="primary-sidebar-inner">
							<div class="card mb-4">
								<div class="card-body px-6 py-4">
								<h4 class="card-title fs-16 lh-2 text-dark mb-3">Find your home</h4>
								<form>
									<div class="form-group">
										<label for="key-word" class="sr-only">Key Word</label>
										<input type="text" class="form-control form-control-lg border-0 shadow-none" id="key-word" name="search" placeholder="Enter keyword...">
									</div>
									<div class="form-group">
										<label for="state" class="sr-only">State</label>
										<select class="form-control border-0 shadow-none form-control-lg selectpicker" name="state" title="States" data-style="btn-lg py-2 h-52" id="state_name" onchange="getRegionByStateId(this.value,'selectpicker')">
											<option value="">Select State</option>
											@foreach ($states as $state)
												<option value="{{$state->id}}">{{$state->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="location" class="sr-only">Region</label>
										<select class="form-control border-0 shadow-none form-control-lg selectpicker" name="region" title="Region" data-style="btn-lg py-2 h-52" id="region_name" onchange="getCityByRegionId(this.value,'selectpicker')">
											<option value="">Select Region</option>
										</select>
									</div>
									<div class="form-group">
										<label for="location" class="sr-only">City</label>
										<select class="form-control border-0 shadow-none form-control-lg selectpicker" name="city" title="City" data-style="btn-lg py-2 h-52" id="city_name">
											<option value="">Select city</option>
		
										</select>
									</div>
									<div class="form-group">
										<label for="property_type" class="sr-only">Type</label>
										<select class="form-control border-0 shadow-none form-control-lg selectpicker" name="bussiness_category" title="Select Bussiness Category" data-style="btn-lg py-2 h-52" id="property_type">
											<option value="">Select Bussiness Category</option>
											@foreach ($businessCategories as $businessCategory)
												<option value="{{$businessCategory->id}}">{{$businessCategory->name}}</option>
											@endforeach
										</select>
									</div>
									<button type="submit"
										class="btn btn-primary btn-lg btn-block shadow-none mt-4">Search
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
                <div class="col-lg-8 mb-8 mb-lg-0 order-1 order-lg-2">
					@foreach ($partnerListings as $partnerListing)
						<div class="py-5 px-4 border rounded-lg shadow-hover-1 bg-white mb-4" data-animate="fadeInUp">
							<div class="media flex-column flex-sm-row no-gutters partnerlisting">
								<div class="col-sm-3 mr-sm-5 card border-0 hover-change-image mb-sm-5">
									<div class="slick-slider photoslider mx-0" data-slick-options="{&quot;slidesToShow&quot;: 1, &quot;autoplay&quot;:true, &quot;dots&quot;:false}">
										@foreach ($partnerListing->partnerListingGalleryImage as $partnerListingGalleryImage)
											<div class="box px-0">
												<div class="card border-0">
													<img src="{{url('/public/storage/upload/partner_listing/gallery_image/'.$partnerListingGalleryImage->image)}}" class="card-img" alt="">
												</div>
											</div>
										@endforeach
									</div>
								</div>
								<div class="description-text media-body mt-3 mt-sm-0">
									<h2>{{$partnerListing->title}}</h2>
									<p class="mb-1 font-weight-500 text-gray-light">
										<i class="fa fa-map-marker" aria-hidden="true"></i> 
										{{$partnerListing->address}}
									</p>
									<p class="mb-2 ml-0">{{$partnerListing->description}}
										<a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal"style="font-size:12px;">Read More</a>
									</p>
									<ul>
										<li>
											<a href="#">
												<i class="fa fa-envelope" aria-hidden="true"></i>
												{{$partnerListing->email}}
											</a>
										</li>
										<li>
											<a href="#">
												<i class="fa fa-phone" aria-hidden="true"></i>
												{{$partnerListing->phone}}
											</a>
										</li>
									</ul>
									<div class="locationButton">
										<a href="{{$partnerListing->website}}"><i class="fa fa-globe" aria-hidden="true"></i> View Website</a>
										<a href="{{$partnerListing->location}}"><i class="fa fa-street-view" aria-hidden="true"></i>Get Location</a>
									</div>
								</div>
							</div>
						</div>
					@endforeach
					<nav class="pt-6">
						<ul class="pagination rounded-active justify-content-center mb-0">
							@if ($partnerListings->lastPage() >1)
								<li class="{{ ($partnerListings->currentPage() == 1) ? ' disabled' : '' }} page-item">
									<a class="page-link" href="{{ $partnerListings->url($partnerListings->currentPage()-1) }}">
										<i class="far fa-angle-double-left"></i>
									</a>
								</li>
								@for ($i = 1; $i <= $partnerListings->lastPage(); $i++)
									<li class="page-item {{ ($partnerListings->currentPage() == $i) ? ' active' : '' }}"><a class="page-link" href="{{$partnerListings->url($i) }}">{{$i}}</a></li> 
								@endfor
								<li class="page-item {{ ($partnerListings->currentPage() == $partnerListings->lastPage()) ? ' disabled' : '' }}">
									<a class="page-link" href="{{ $partnerListings->url($partnerListings->currentPage()+1) }}">
										<i class="far fa-angle-double-right"></i>
									</a>
								</li>
							@endif
						</ul>
					</nav>
                </div>
            </div>
            </div>
      	</section>
   	</main>
@endsection
@push('js')
<script src="{{asset('assets/custom.js')}}"></script>
	
@endpush
