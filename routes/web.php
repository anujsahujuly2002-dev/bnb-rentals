<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::namespace('Frontend')->group(function() {
    Route::controller('FrontendController')->name('frontend.')->group(function() {
        Route::get('/','index')->name('index');
        Route::get('/about-us','aboutUs')->name('abouts');
        Route::get('/about-us','aboutUs')->name('abouts');
        Route::get('/property-listing','propertyListing')->name('property.listing');
        Route::get('/contact-us','contactUs')->name('contact.us');
        Route::get('/list-our-property','listOurProperty')->name('list.our.property');
        Route::post('/sugesstion-destination','destintaionSuggestion')->name('destination.suggestion');
        Route::get('/partner-listing','partnerListing')->name('partner.listing');
        Route::post('/calender','calender');
        Route::get('/property/ical-link/{id}','genratePropertIcalLink');
    });
    Route::controller('PropertyListingController')->group(function () {
        Route::get('/property-details/p{id}','propertyListingDetails')->name("property.listing.details");
        Route::get('/location-property','locationProperty')->name('location.property');
        Route::post('/property-enquiry-store','propertyEnquiryStore')->middleware('check-user-loggedin');
        Route::post('/store-reviews-rating','StoreReviewsRating');
        Route::post('/calculte-rate','calculateRate')->name('calculate.rate');
    });

    Route::controller('AuthController')->middleware(['back-prevent-history','guest'])->prefix('auth')->group(function() {
        Route::post('owner-register','OwnerRegistration')->name('owner.registration');
        Route::post('owner-login','ownerLogin');
    });

    // Booking Route

    Route::controller(BookingInformationController::class)->group(function () {
        Route::post('/store-booking-information','storeBookingInformation')->middleware('check-user-loggedin');
        Route::get('/booking-information','bookingInformation')->name('booking.information');
        Route::post('/make-payment','makePayment')->name('make.paymnet');
        Route::get('/payment-success', function () {
            return view('frontend.payment-success');
        })->name('payment.success');
        Route::get('/payment-failed', function () {
            return view('frontend.payment-error');
        })->name('payment.failed');
    });

    Route::controller(ChatController::class)->group(function(){
        Route::get('/chat','chat')->name('chat');
        Route::get('/get-user','getUser');
        Route::post('/insert-chat','InsertChat');
        Route::post('/get-chat','getChat');
    });

    Route::controller(CancelBookingController::class)->prefix('cancel-booking')->group(function(){
        Route::get('cancel/{cancel_id}','cancelBooking')->name('cancel.booking');
        Route::post('/store','cancelBookingStore')->name('cancel.booking.store');
        Route::get('/list','cancelBookingList')->name('cancel.bokking.list');
    });
});


