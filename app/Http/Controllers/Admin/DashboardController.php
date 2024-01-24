<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Session;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PartnerListing;
use App\Models\PropertyListing;
use App\Models\BookingInformation;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index () {
        $totalProperties = PropertyListing::count();
        $featureListing = PropertyListing::where('feature','1')->count();
        $partnerListing = PartnerListing::count();
        $totalBooking = BookingInformation::where('status','confirmed')->count();
        $users = User::whereHas('roles',function($q){
            $q->whereNot('name','super-admin');
        })->latest()->take(10)->get();
        return view('admin.dashboard',compact('totalProperties','featureListing','partnerListing','totalBooking','users'));
    }

    // Logout Method
    public function logout() {
        Session::flush();
        Auth::logout();
        return to_route('admin.login');
    }
}
