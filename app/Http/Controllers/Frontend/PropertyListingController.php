<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use App\Models\State;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\PropertyRates;
use App\Models\PropertyBooking;
use App\Models\PropertyEnquiry;
use App\Models\PropertyListing;
use App\Mail\PropertyEnquiryMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\PropertyReviewsRating;
use App\Notifications\EnquiryNotification;

class PropertyListingController extends Controller
{
    public function propertyListingDetails ($id) {
        $PropertyListing = PropertyListing::where('id',$id)->first();
        $similarProperties = PropertyListing::where(['city_id'=>$PropertyListing->city_id ])->whereNot('id',$PropertyListing->id)->take(3)->get();
        return view('frontend.property-details',compact('PropertyListing','similarProperties'));
    }

    public function propertyEnquiryStore(Request $request){
        $property = PropertyListing::where(['id'=>$request->input("property_id"),'approval'=>'1'])->first();
        $enquiriesDetails = [
           'owner_id'=>$property->user_id,
           'property_id'=>$property->id,
           'traveller_id'=>auth()->user()->id,
           'check_in'=>Carbon::parse($request->input('check_in'))->format('Y-m-d'),
           'check_out'=>Carbon::parse($request->input('check_out'))->format('Y-m-d'),
           'no_of_guest'=>$request->input('no_of_guest'),
           'message' =>$request->input('description'),
        ];
        $travellerName= auth()->user()->name;
        $user=User::findOrFail($property->user_id);
        $enquiriesDetailsStore = PropertyEnquiry::create($enquiriesDetails);
        $user->notify(new EnquiryNotification($user,$property,$request,$travellerName));
        if($enquiriesDetailsStore):
            Chat::create([
                'sender_id'=>auth()->user()->id,
                'reciver_id'=>$property->user_id,
                'msg'=>$request->input('description')
            ]);
            return response()->json([
                'status'=>'1',
                'msg'=>"Enquiry Send Successfully "
            ]);
        else:
            return response()->json([
                'status'=>'0',
                'msg'=>"Enquiry Not Send !, Please try again"
            ]);
        endif;
    }

    public function StoreReviewsRating(Request $request){
        $propertyReviews = PropertyReviewsRating::create([
            'property_id' => $request->input('property_id'),
            'guest_name' => $request->input('name'),
            'reviews' => $request->input('message'),
            'rating' => $request->input('rate'),
            'email' => $request->input('email')
        ]);
        if( $propertyReviews):
            return response()->json([
                'status'=>1,
                'msg'=>'Reviews Submit Successfully,'
            ]);

        else:
            return response()->json([
                'status'=>0,
                'msg'=>'Reviews Not Submit,Please try again'
            ]);
        endif;
    }

    public function locationProperty() {
        $states = State::where('country_id',1)->get();
        return view('frontend.location-property',compact('states'));
    }

    public function calculateRate(Request $request){
        $checkInDate = Carbon::parse($request->input('start_date'));
        $checkOutDate = Carbon::parse($request->input('end_date'));
        $totalNight = $checkOutDate->diffInDays($checkInDate);
        $checkAvailablity = PropertyBooking::where('property_id',$request->input('property_id'))->where('end_date','<=',$checkOutDate->format('Y-m-d'))->where('start_date','>=',$checkInDate->format('Y-m-d'))->get();
        if($checkAvailablity->count() > 0):
            return response()->json([
                'status'=>500,
                'msg'=>'Some dates are already booked,Choose available dates only.'
            ],500);
        endif;
        $minmumStay =PropertyRates::where(['property_id'=>$request->input('property_id')])->where('from_date', '<=',$checkInDate->format('Y-m-d'))->first();
        $minmumStayNight = $minmumStay !=null?$minmumStay->minimum_stay:1;
        if($minmumStayNight >$totalNight):
            return response()->json([
                'status'=>500,
                'msg'=>'Please select minimum '.$minmumStayNight.' nights for booking.'
            ],500);
        endif;
        $dateRange = CarbonPeriod::create($checkInDate,$checkOutDate->subDays(1) );
        $grossAmount = 0;
        $dates = array_map(fn($date)=>$date->format('Y-m-d'),iterator_to_array($dateRange));
        for($i =0 ;$i<count($dates);$i++):
            $property_rates = PropertyRates::where(['property_id'=>$request->input('property_id')])->where('from_date', '<=',$dates[$i])->where('to_date','>=',$dates[$i])->first();
           if($property_rates !=null):
                if($property_rates->nightly_rate !=null):
                    $grossAmount +=$property_rates->nightly_rate;
                elseif($property_rates->weekly_rate !=null && $property_rates->nightly_rate ==null):
                    $oneNight = $property_rates->weekly_rate/7;
                    $grossAmount +=$oneNight;
                elseif($property_rates->monthly_rate !=null && $property_rates->weekly_rate ==null && $property_rates->nightly_rate ==null):
                    $oneNight = $property_rates->monthly_rate/30;
                    $grossAmount +=$oneNight;
                endif;
            else:
                /* $property_rates = PropertyRate::where('property_id',$request->input('property_id'))->whereNotNull('start_date')->first();
                if($property_rates !=null):
                    if($property_rates->nightly_rate !=null):
                        $grossAmount +=$property_rates->nightly_rate;
                    elseif($property_rates->weekly_rate !=null && $property_rates->nightly_rate ==null):
                        $oneNight = $property_rates->weekly_rate/7;
                        $grossAmount +=$oneNight;
                    elseif($property_rates->monthly_rate !=null && $property_rates->weekly_rate ==null && $property_rates->nightly_rate ==null):
                        $oneNight = $property_rates->monthly_rate/30;
                        $grossAmount +=$oneNight;
                    endif;
                else: */
                    $property = PropertyListing::where('id',$request->input('property_id'))->first()->avg_night_rates;
                    $grossAmount +=$property;
                /* endif; */
           endif;
        endfor;
        $property = PropertyListing::where('id',$request->input('property_id'))->first();
        $tatalAmount = ($grossAmount);
        $html = '<div class="row"> <div class="col-md-6">'.$totalNight.' Nights :</div><div class="col-md-6 text-right">$'.number_format($grossAmount,2).'</div></div>';
        if($property->admin_fees !=null):
            $tatalAmount  +=$property->admin_fees;
            $html .=  '<div class="row"> <div class="col-md-6">Admin Fees:</div><div class="col-md-6 text-right">$'.number_format(($property->admin_fees),2).'</div></div>';
        endif;
        if($property->cleaning_fees !=null):
            $tatalAmount  +=$property->cleaning_fees;
            $html .=  '<div class="row"> <div class="col-md-6">Cleaning Fees:</div><div class="col-md-6 text-right">$'.number_format(($property->cleaning_fees),2).'</div></div>';
        endif;
        if($property->refundable_damage_deposite !=null):
            $tatalAmount  +=$property->refundable_damage_deposite;
            $html .=  '<div class="row"> <div class="col-md-6">Ref. Dmge Amount:</div><div class="col-md-6 text-right">$'.number_format(($property->refundable_damage_deposite),2).'</div></div>';
        endif;
        if($property->danage_waiver !=null):
            $tatalAmount  +=$property->danage_waiver;
            $html .=  '<div class="row"> <div class="col-md-6">Dmge Waiver Amt:</div><div class="col-md-6 text-right">$'.number_format(($property->danage_waiver),2).'</div></div>';
        endif;
        if($property->peet_fee !=null && $request->input('pet_fees') =='1'):
            if($property->pet_fees_unit =='Per Day'):
                $petFees = $property->peet_fee;
                $tatalAmount = ($tatalAmount+($petFees*$totalNight));
                $totalPetFees = $petFees*$totalNight;
            elseif($property->pet_fees_unit =='Per Week'):
                $oneday = $property->peet_fee/7;
                $tatalAmount = ($tatalAmount+($oneday*$totalNight));
                $totalPetFees = $oneday*$totalNight;
            else:
                $petFees = $property->peet_fee;
                $tatalAmount = ($tatalAmount+$petFees);
                $totalPetFees = $petFees;
            endif;
            $html .=  '<div class="row"> <div class="col-md-6">Pet Fee:</div><div class="col-md-6 text-right">$'.number_format(($totalPetFees),2).'</div></div>';
        endif;
        if($property->peet_fee !=null && $request->input('pool_heating') =='1'):
            if($property->pool_heating_fees_perday =='Per Day'):
                $poolHeatingFees = $property->poolheating_fee;
                $tatalAmount = ($tatalAmount+($poolHeatingFees*$totalNight));
                $totalPetFees = $poolHeatingFees*$totalNight;
            elseif($property->pool_heating_fees_perday =='Per Week'):
                $oneday = $property->poolheating_fee/7;
                $tatalAmount = ($tatalAmount+($oneday*$totalNight));
                $totalPetFees = $oneday*$totalNight;
            else:
                $poolHeatingFees = $property->poolheating_fee;
                $tatalAmount = ($tatalAmount+$poolHeatingFees);
                $totalPetFees = $poolHeatingFees;
            endif;
            $html .=  '<div class="row"> <div class="col-md-6">Pool Heatings Fee:</div><div class="col-md-6 text-right">$'.number_format(($totalPetFees),2).'</div></div>';
        endif;
        if($property->extra_person_fee !=null && $property->after_guest < ($request->input('adult')+$request->input('child'))):
            $extraPerson = ((float)$request->input('adult') + $request->input('child')) - $property->after_guest;
            $tatalAmount  +=$property->extra_person_fee*$extraPerson*$totalNight;
            $html .=  '<div class="row"> <div class="col-md-6">Extra Person Fees:(After '.$property->after_guest.' Guests):</div><div class="col-md-6 text-right">$'.number_format(($property->extra_person_fee*$extraPerson*$totalNight),2).'</div></div>';
        endif;
        if($property->tax_rates !=null):
            if($property->refundable_damage_deposite !=null):
                $taxableAmount = $tatalAmount - $property->refundable_damage_deposite;
            endif;
                $tatalAmount += $taxableAmount*$property->tax_rates/100;
            $html .= '<div class="row"> <div class="col-md-6"><strong>Total Tax</strong></div><div class="col-md-6 text-right"><strong>$'.number_format($taxableAmount*$property->tax_rates/100,2).'</strong></div></div>';
        endif;
        $html .= '<div class="row"> <div class="col-md-6"><strong>Total Amount</strong></div><div class="col-md-6 text-right"><strong>$'.number_format($tatalAmount,2).'</strong></div></div>';
        return response()->json([
            'data'=>$html,
         ],200);
    }
}
