@extends('owner.layouts.master')
@push('css')
    @if (!empty($propertyListing))
        <style>
            .new-property-step a {
                pointer-events: auto;
            }

            .image_container {
                height: 120px;
                width: 150px;
                border-radius: 6px;
                overflow: hidden;
                margin: 10px;
                display: inline-block;
                vertical-align: top;
                position: relative;
            }

            .image_container img {
                height: 100%;
                width: auto;
                object-fit: cover;
            }

            .image_container span {
                top: 6px;
                right: 6px;
                color: red;
                font-size: 16px;
                font-weight: normal;
                cursor: pointer;
                position: absolute;
                background: #fff;
                padding: 5px 8px;
                display: block;
                border-radius: 50%;
                width: 28px;
                height: 28px;
                line-height: 20px;
            }

           
            .fc .fc-scroller-liquid-absolute { 
                position: relative !important;
            }
            .fc-daygrid-body { width: 100% !important; }
            .fc-scrollgrid-sync-table { width: 100% !important; }
            .fc-col-header  { width: 100% !important; }
        </style>        
    @endif
@endpush
@section('content')
    <main id="content" class="bg-gray-01">
        <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10 my-profile">
            <div class="mb-6">
                <h2 class="mb-0 text-heading fs-22 lh-15">Add new property</h2>
            </div>
            <div class="collapse-tabs new-property-step">
                <ul class="nav nav-pills d-none d-md-flex mb-6" role="tablist">
                    <li class="nav-item col p-1">
                        <a class="nav-link active bg-transparent shadow-none font-weight-500 text-center lh-214 d-block"
                            id="description-tab" data-toggle="pill" data-number="1." href="#description" role="tab"
                            aria-controls="description" aria-selected="true"><span class="number">1.</span> Description</a>
                    </li>
                    <li class="nav-item col p-1">
                        <a class="nav-link bg-transparent shadow-none font-weight-500 text-center lh-214 d-block"
                            id="amenities-tab" data-toggle="pill" data-number="2." href="#amenities" role="tab"
                            aria-controls="amenities" aria-selected="false"><span class="number">2.</span> Amenities</a>
                    </li>
                    <li class="nav-item col p-1">
                        <a class="nav-link bg-transparent shadow-none font-weight-500 text-center lh-214 d-block"
                            id="location-tab" data-toggle="pill" data-number="3." href="#locations" role="tab"
                            aria-controls="location" aria-selected="false"><span class="number">3.</span> Location</a>
                    </li>
                    <li class="nav-item col p-1">
                        <a class="nav-link bg-transparent shadow-none font-weight-500 text-center lh-214 d-block"
                            id="rental-rates-tab" data-toggle="pill" data-number="4." href="#rental-rates" role="tab"
                            aria-controls="rental-rates" aria-selected="false"><span class="number">4.</span> Rental
                            Rates</a>
                    </li>
                    <li class="nav-item col p-1">
                        <a class="nav-link bg-transparent shadow-none font-weight-500 text-center lh-214 d-block"
                            id="photo-tab" data-toggle="pill" data-number="5." href="#photo" role="tab"
                            aria-controls="photo" aria-selected="false"><span class="number">5.</span> Photos</a>
                    </li>
                    <li class="nav-item col p-1">
                        <a class="nav-link bg-transparent shadow-none font-weight-500 text-center lh-214 d-block"
                            id="rental-policies-tab" data-toggle="pill" data-number="6." href="#rental-policies"
                            role="tab" aria-controls="rental-policies" aria-selected="false"><span
                                class="number">6.</span> Rental Policies</a>
                    </li>
                    <li class="nav-item col p-1">
                        <a class="nav-link bg-transparent shadow-none font-weight-500 text-center lh-214 d-block"
                            id="calender-tab" data-toggle="pill" data-number="7." href="#calender" role="tab"
                            aria-controls="calender" aria-selected="false"><span class="number">7.</span>Calender</a>
                    </li>
                    {{-- <li class="nav-item col p-1">
                        <a class="nav-link bg-transparent shadow-none font-weight-500 text-center lh-214 d-block"
                            id="reviews-tab" data-toggle="pill" data-number="8." href="#reviews" role="tab"
                            aria-controls="reviews" aria-selected="false"><span class="number">8.</span>Reviews</a>
                    </li> --}}
                </ul>
                <div class="tab-content shadow-none p-0">
                    <form id="listing_form">
                        <input type="hidden" name="property_listing_id" value="{{ $propertyListing->id ?? '' }}">
                        <input type="hidden" name="user_id" value="{{ Auth()->user()->id }}">
                        <div id="collapse-tabs-accordion">
                           @include('owner.property-partial.information')
                           @include('owner.property-partial.amenities')
                           @include('owner.property-partial.location')
                           @include('owner.property-partial.rental-rates')
                           @include('owner.property-partial.gallery-mages')
                           @include('owner.property-partial.rental-policies')
                           @include('owner.property-partial.calender')
                           {{-- @include('owner.property-partial.reviews') --}}
                        </div>
                    </form>
                </div>
                {{-- Rental Rates Update Model start  --}}
                <div class="modal fade" id="rental_rates_edit" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Update Rental Rates Form</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="update_rental_rates">
                                    @csrf
                                    <input type="hidden" name="id" value="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="session_name" class="col-form-label">Season Name:</label>
                                                <input type="text" class="form-control" id="session_name"
                                                    value="" name="session_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="from_date" class="col-form-label">From Date:</label>
                                                <input type="date" class="form-control" id="from_date"
                                                    name="from_date">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="to_date" class="col-form-label">To Date:</label>
                                                <input type="date" class="form-control" id="to_date" name="to_date"
                                                    value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nightly_rate" class="col-form-label">Nightly Rate:</label>
                                                <input type="text" class="form-control" id="nightly_rate"
                                                    name="nightly_rate" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weekly_rate" class="col-form-label">Weekly Rate:</label>
                                                <input type="text" class="form-control" id="weekly_rate"
                                                    name="weekly_rate" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weekend_rate" class="col-form-label">Weekend Rate:</label>
                                                <input type="text" class="form-control" id="weekend_rate"
                                                    name="weekend_rate" value="weekend_rate">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="monthly_rate" class="col-form-label">Monthly Rate:</label>
                                                <input type="text" class="form-control" id="monthly_rate"
                                                    name="monthly_rate" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="minimum_stay" class="col-form-label">Minimum Stay:</label>
                                                <input type="text" class="form-control" id="minimum_stay"
                                                    name="minimum_stay" value="">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary update_rental_rates">Update Rental
                                    Rates</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Rental Rates Update Model end  --}}
                {{-- Riviews Rating Model Start --}}
                <div class="modal fade" id="revies_rating" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Reviews Rating Update Form</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="update_reviews_rating">
                                    @csrf
                                    <input type="hidden" name="id" value="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reviews_heading" class="col-form-label">Reviews
                                                    Heading:</label>
                                                <input type="text" class="form-control" id="reviews_heading"
                                                    value="" name="reviews_heading">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="guest_name" class="col-form-label">Guest Name:</label>
                                                <input type="text" class="form-control" id="guest_name"
                                                    name="guest_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="place" class="col-form-label">Place:</label>
                                                <input type="text" class="form-control" id="place"
                                                    name="place" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reviews" class="col-form-label">Reviews:</label>
                                                <textarea type="text" class="form-control" id="reviews" name="reviews"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group rating">
                                                <label for="rating_update" class="col-form-label">Rating</label><br>
                                                <select name="rating_update" id="rating_update" class="form-control">
                                                    <option value="">Select Rating</option>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }} Star
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary update_reviews">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Riviews Rating Model end --}}
                {{-- Rental Rates Update Model start  --}}
                <div class="modal fade" id="bookingEvent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Manual Booking Form </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="createManualBookings"> 
                                <div class="modal-body">
                                    <input type="hidden" name="property_id" value="" id="property_id">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_name" class="col-form-label">Customer Name</label>
                                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="start_date" class="col-form-label">Start Date</label>
                                                <input type="date" class="form-control start_date" id="start_date" name="start_date" value="" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="end_date" class="col-form-label">End Date</label>
                                                <input type="date" class="form-control end_date" id="end_date" name="end_date" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Book Calender</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Rental Rates Update Model end  --}}
            </div>
        </div>
    </main>
@endsection
@push('js')
    <script src="{{ asset('assets/custom.js') }}"></script>
    <script src="{{ asset('owner-assets/customJs/property_information.js') }}"></script>
    <script src="{{ asset('owner-assets/customJs/aminities_attraction.js') }}"></script>
    <script src="{{ asset('owner-assets/customJs/location_info.js') }}"></script>
    <script src="{{ asset('owner-assets/customJs/rental_rates.js') }}"></script>
    <script src="{{ asset('owner-assets/customJs/gallery_image.js') }}"></script>
    <script src="{{ asset('owner-assets/customJs/rental-policies.js') }}"></script>
    <script src="{{ asset('owner-assets/customJs/calender_syncronization.js') }}"></script>
    <script src="{{ asset('owner-assets/customJs/reviews_rating.js') }}"></script>
    <script src="{{ asset('owner-assets/js/map.js') }}"></script>
    {{-- <script src="{{asset('assets/custom/map.js')}}"></script> --}}
    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&callback=initMap">
    </script>
    <script>
        $(document).ready(function(){
            getEvent();
        })

        createManualBookings.onsubmit = async (e) =>{
            try{
                showLoader();
                e.preventDefault();
                const response = await fetch("{{route('admin.property.listing.block.manual.booking')}}",{
                    method:"POST",
                    body: new FormData(createManualBookings),
                    processData: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });
                const results = await response.json();
                if(response.status==200){
                    hideLoader();
                    getEvent();
                    $("#createManualBookings")[0].reset();
                    $("#bookingEvent").modal('hide');
                    toastr.success(results.msg);
                }else if(results.status==500){
                    hideLoader();
                    toastr.error( results.msg);
                }else{
                    hideLoader();
                    toastr.error( response.statusText);
                }
            }catch(err){
                toastr.error(err.message);
                console.log(err.message);
            }
        }
        
    </script>
@endpush
