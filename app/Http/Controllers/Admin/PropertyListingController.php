<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyType;
use App\Models\Country;
use App\Models\MainAminity;
use App\Http\Requests\PropertyListing\PropertyListingRequestStepOne;
use App\Http\Requests\PropertyListing\PropertyRatesRequest;
use App\Http\Requests\PropertyListing\PropertyListingRequestStepThree;
use App\Models\CancellentionPolicy;
use App\Models\PropertyListing;
use App\Models\PropertiesAminites;
use App\Models\SubAminities;
use App\Models\PropertyRates;
use App\Models\Currency;
use App\Models\PropertyGallery;
use App\Models\PropertyBooking;
use App\Models\PropertyReviewsRating;
use App\Models\State;
use App\Models\Region;
use App\Models\City;
use App\Models\Cities;
use App\Models\User;
use Carbon\Carbon;
use App\Models\ImportIcal;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;

class PropertyListingController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()):
            $propertyListing = PropertyListing::latest()->with('region','city','sub_city');
            return Datatables::of($propertyListing)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if($request->get('property_id') != ''):
                        $instance->where('id', $request->get('property_id'));
                    elseif($request->get('email') != ''):
                        $user = User::where('email',$request->get('email'))->first();
                        $instance->where('user_id', $user->id);
                    elseif($request->get('name') != ''):
                        $user = User::where('name',$request->get('name'))->first();
                        $instance->where('user_id', $user->id);
                    endif;
                })
                ->editColumn('property_main_photos',function($row) {
                    return '<img src="'.url('public/storage/upload/property_image/main_image/'.$row->property_main_photos).'" class=" rounded-circle mr-3" height="50" width="50">';
                })
                ->addColumn('action', function($row){
                    $actionBtn = '<a href="'.route('admin.property.listing.create',['id'=>encrypt($row->id)]).'" class="edit btn btn-success btn-sm">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" onclick="propertyDelete('.$row->id.')">Delete</a>';
                    return $actionBtn;
                })
                ->addColumn('property_approved',function($row){
                    $checkedAttr = "";
                    $value = "1";
                    if($row->subscription_date !=null && $row->approval=='1'):
                        $checkedAttr = "checked";
                        $value = "0";
                    endif;
                    return '<label class="switch"><input type="checkbox"'.$checkedAttr.' value="'.$value.'"class="property_approval" onchange="propertyApproved('.$row->id.','.$value.')"><span class="slider-green round" ></span></label>';
                })
                ->addColumn('renval_date',function($row){
                   return $row->subscription_date !=null ?date('d-M-Y', strtotime("+12 months", strtotime($row->subscription_date))):"NA";
                    
                })
                ->addColumn('created_date',function($row){
                    return date('d-M-Y', strtotime("+12 months", strtotime($row->created_at)));
                })
                ->editColumn('subscription_date',function($row){
                    return $row->subscription_date !=null ?date('d-M-Y',strtotime($row->subscription_date)):"NA";
                })
                ->addColumn("featured_approved",function($row){
                    $checkedAttr = "";
                    $value = "1";
                    if($row->feature =='1'):
                        $checkedAttr = "checked";
                        $value = "0";
                    endif;
                    return '<label class="switch"><input type="checkbox"'.$checkedAttr.' value="'.$value.'"class="feature_property" onchange="propertyFeatured('.$row->id.','.$value.')"><span class="slider-green round" ></span></label>';
                })
                ->addColumn('no_enquires',function($row){
                    return $row->enquires->count();
                })
                ->rawColumns(['action','property_main_photos','property_approved','subscription_date','renval_date','featured_approved','no_enquires'])
                ->make(true);
        endif;
        return view('admin.property-listing.index');
    }

    public function create($propert_id=null) {
        $propertyTypes = PropertyType::get();
        $countries = Country::get();
        $mainAminity = MainAminity::with('subAminities')->get();
        $currencies = Currency::get();
        $cancelletionPolicies = CancellentionPolicy::get();
        $propertyListing = "";
        $states="";
        $regions="";
        $cities="";
        $sub_cities="";
        if(!is_null($propert_id)){
            $propertyListing = PropertyListing::where("id",decrypt($propert_id))->first();
            $states = State::where('country_id',$propertyListing->country_id)->get();
            $regions = Region::where('state_id',$propertyListing->state_id)->get();
            $cities = City::where('region_id',$propertyListing->region_id)->get();
            $sub_cities = Cities::where('city_id',$propertyListing->city_id)->get();
        }
        return view('admin.property-listing.create',compact('propertyTypes','countries','mainAminity','currencies','propertyListing','states','regions','cities','sub_cities','cancelletionPolicies'));
    }

    public function store(PropertyListingRequestStepOne $request) {
        if($request->hasfile('property_main_image')):
            $path = storage_path('public/upload/property_image/main_image/');
            if(file_exists($path.$request->input('property_old_image'))):
                unlink($path.$request->input('property_old_image'));
            endif;
            $image = $request->file('property_main_image');
            $ext = "webp";
            $convertImage = Image::make($image->getRealPath())->resize(650, 960, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($ext,100);
            $originalImageName = uniqid().'.'.$ext;
            Storage::put('public/upload/property_image/main_image/'.$originalImageName, $convertImage);
        endif;
        $avgNights = explode(" ",$request->input('avg_night'));
        if($request->input('property_listing_id') ==null):
           
            $propertyListing = PropertyListing::create([
                'user_id' =>Auth::user()->id,
                'property_name' =>$request->input('property_name'),
                'property_main_photos' => $originalImageName,
                'square_feet' => $request->input('square_feet'),
                'property_type_id' => $request->input('property_type'),
                'bedrooms' => $request->input('bedrooms'),
                'sleeps'=> $request->input('sleeps'),
                'avg_night_rates' =>  $avgNights[0],
                'avg_rate_unit' => $avgNights[1].' '.$avgNights[2],
                'baths' => $request->input('baths'),
                'description' => $request->input('description'),
                'country_id' => $request->input('country'),
                'state_id' => $request->input('state'),
                'region_id' => $request->input('region'),
                'city_id' => $request->input('city'),
                'sub_city_id' => $request->input('sub_city') !=null?$request->input('sub_city'):NULL,
                'address' => $request->input('address'),
                'town' => $request->input('town'),
                'zip_code' => $request->input('zipcode'),
                'youtube_video_link'=>$request->input('youtube_video_link')
            
            ]);
        else:
            $propertyListing = PropertyListing::where('id',$request->input('property_listing_id'))->update([
                'user_id' =>Auth::user()->id,
                'property_name' =>$request->input('property_name'),
                'property_main_photos' => $originalImageName??$request->input("property_old_image"),
                'square_feet' => $request->input('square_feet'),
                'property_type_id' => $request->input('property_type'),
                'bedrooms' => $request->input('bedrooms'),
                'sleeps'=> $request->input('sleeps'),
                'avg_night_rates' =>  $avgNights[0],
                'avg_rate_unit' => $avgNights[1].' '.$avgNights[2],
                'baths' => $request->input('baths'),
                'description' => $request->input('description'),
                'country_id' => $request->input('country'),
                'state_id' => $request->input('state'),
                'region_id' => $request->input('region'),
                'city_id' => $request->input('city'),
                'sub_city_id' => $request->input('sub_city') !=null?$request->input('sub_city'):NULL,
                'address' => $request->input('address'),
                'town' => $request->input('town'),
                'zip_code' => $request->input('zipcode'),
                'youtube_video_link'=>$request->input('youtube_video_link')
            ]);
        endif;
        if($propertyListing):
            return response()->json([
                'status'=>'1',
                'property_id' =>$propertyListing->id??$request->input('property_listing_id')
            ]);
        else:
            return response()->json([
                'status'=>'0',
            ]);
        endif;

    }

    public function stepTwoStore(Request $request) {
        // dd($request->all());
        $properties_aminities = PropertiesAminites::where("property_id",$request->input('property_id'))->get();
        if( $properties_aminities->count() >0):
            $properties_aminities->each->delete();
        endif;
        foreach($request->input('sub_aminities_id') as $subAminities):
            $mainAminity =SubAminities::where('id',$subAminities)->first();
            $subAminity = PropertiesAminites::create([
                'property_id' => $request->input('property_id'),
                'aminities_id' =>$mainAminity->main_aminities_id,
                'sub_aminities_id'=>$subAminities
            ]);
        endforeach;
        if($subAminity):
            return response()->json([
                'status'=>'1',
                'property_id' =>$request->input('property_id')
            ]);
        else:
            return response()->json([
                'status'=>'0',
            ]);
        endif;
    }


    public function propertyRateStore(PropertyRatesRequest $request) {
       $propertyRates = PropertyRates::create([
            "property_id"=>$request->input('property_listing_id'),
            "session_name"=>$request->input('session_name'),
            "from_date"=>$request->input('from_date'),
            "to_date"=>$request->input('to_date'),
            "nightly_rate"=>$request->input('nightly_rate'),
            "weekly_rate"=>$request->input('weekly_rate'),
            "weekend_rates"=>$request->input('weekend_rate'),
            "monthly_rate"=>$request->input('monthly_rates'),
            "minimum_stay"=>$request->input('minimum_stay'),
            "additional_person"=>$request->input('additional_persons'),
            "other_fess"=>$request->input('other_fees'),
       ]);
       if($propertyRates):
        return response()->json([
            'status'=>'1',
            "msg"=>"Property Rates Added successfully"
        ]);
       else:
        return response()->json([
            'status'=>'0'
        ]);
       endif;
    }

    public function getPropertyRates(Request $request) {
        // dd("test");
        if($request->ajax()):
            $propertyRates =[];
            if($request->input("property_id") !=""):
                $propertyRates = PropertyRates::where('property_id',$request->input("property_id"))->latest();
            endif;
           return Datatables::of($propertyRates)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm" onclick="editRentalRates('.$row->id.')">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" onclick="rentalRatesDelete('.$row->id.')">Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action',])
                ->make(true);
        endif;
    }

    public function rentalRatesStore(PropertyListingRequestStepThree $request) {
       $propertyListing = PropertyListing::where('id',$request->input('property_listing_id'))->update([
            'admin_fees' =>$request->input("admin_fees"),
            'cleaning_fees' =>$request->input("cleaning_fees"),
            'refundable_damage_deposite' =>$request->input("refundable_damage_deposite"),
            'danage_waiver' =>$request->input("danage_waiver"),
            'peet_fee' =>$request->input("peet_fee"),
            'pet_fees_unit' =>$request->input("pet_rate_unit"),
            'extra_person_fee' =>$request->input("extra_person_fee"),
            'after_guest' =>$request->input("after_guest"),
            'poolheating_fee' =>$request->input("poolheating_fee"),
            'pool_heating_fees_perday' =>$request->input("pool_heating_fees_perday"),
            'check_in' =>$request->input("check_in"),
            'check_out' =>$request->input("check_out"),
            'tax_rates' =>$request->input("tax_rates"),
            'change_over' =>$request->input("change_over"),
            'currency_id'=>$request->input("rates"),
            'rates_notes'=>$request->input("rates_notes"),
       ]);
       if($propertyListing):
            return response()->json([
                'property_id'=>$request->input('property_listing_id'),
                'status'=>'1'
            ]);
       else:
            return response()->json([
                'status'=>'0'
            ]);
       endif;

    }

    public function rentalPolicyStore(Request $request){
        $file_name = "";
        $cancel_rental_file_name ="";
        if($request->hasFile('upload_rental_polices')):
            $file = $request->file('upload_rental_polices');
            $ext = $file->getClientOriginalExtension();
            $file_name = uniqid().'.'.$ext;
            $file->move(storage_path('app/public/upload/document/rental_policies/'),$file_name);
        endif;
        if($request->hasFile('upload_cancel_rental_polices')):
            $file = $request->file('upload_cancel_rental_polices');
            $ext = $file->getClientOriginalExtension();
            $cancel_rental_file_name = uniqid().'.'.$ext;
            $file->move(storage_path('app/public/upload/document/rental_policies/'),$cancel_rental_file_name);
        endif;
        $propertyListing = PropertyListing::where('id',$request->input('property_listing_id'))->update([
            'rental_policies'=>$request->input("rental_policies"),
            'upload_rental_polices'=>$file_name,
            'upload_cancel_rental_polices'=>$cancel_rental_file_name,
            'cancelletion_policies_id'=>$request->input("cancel_rental_polices")
       ]);
       if($propertyListing):
            return response()->json([
                'property_id'=>$request->input('property_listing_id'),
                'status'=>'1'
            ]);
        else:
            return response()->json([
                'status'=>'0'
            ]);
        endif;

    }
    // Propperty Location Info store Method start
    public function locationInfoStore(Request $request) {
        $propertyListing = PropertyListing::where('id',$request->input('property_listing_id'))->update([
            'location'=>$request->input("location"),
            'iframe_link_google'=>$request->input("iframe_link_google"),
            'latitude'=>$request->input("lat"),
            'longitude'=>$request->input("long"),
       ]);
       if($propertyListing):
            return response()->json([
                'property_id'=>$request->input('property_listing_id'),
                'status'=>'1'
            ]);
        else:
            return response()->json([
                'status'=>'0'
            ]);
        endif;
    }
    // Propperty Location Info store Method end


    // Property Gallery Image Store Method Start 
    public function galleryImageStore(Request $request){
        // dd($request->all());
        $propertyListing ='';
        if($request->TotalFiles > 0):
            for($x = 0; $x < $request->TotalFiles; $x++):
                if ($request->hasFile('files'.$x)):
                    $image = $request->file('files'.$x);
                    $ext = "webp";
                    $convertImage = Image::make($image->getRealPath())->resize(1000, 720, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->encode($ext,100);
                    $originalImageName = uniqid().'.'.$ext;
                    Storage::put('public/upload/property_image/gallery_image/'.$originalImageName, $convertImage);
                    $propertyListing = PropertyGallery::create([
                        "property_id"=>$request->input("property_listing_id"),
                        "image_name"=>$originalImageName
                    ]);
                endif;
            endfor;
        endif;
        if($propertyListing):
            return response()->json([
                'property_id'=>$request->input('property_listing_id'),
                'status'=>'1'
            ]);
        else:
            return response()->json([
                'status'=>'0'
            ]);
        endif;
    }
    // Property Gallery Image Store Method end 


    // Calender synchronization Method Start Start 

    public function calenderSynchronization(Request $request) {
        try{
            $ical_response = file_get_contents($request->input('import_calender_url'));
            $icsDates = array ();
            $icsData = explode ( "BEGIN:", $ical_response );
            foreach ( $icsData as $key => $value ) {
                $icsDatesMeta [$key] = explode ( "\n", $value );
            }
            foreach ( $icsDatesMeta as $key => $value ) {
                foreach ( $value as $subKey => $subValue ) {
                    $icsDates = $this->getICSDates ( $key, $subKey, $subValue, $icsDates );
                }
            }
            unset($icsDates[1]);
            $property_booking ="";
            foreach ($icsDates as $key => $icsDate) :
                $dateTimeStamp =  date("Y-m-d h:i:s",strtotime($icsDate['DTSTAMP']));
                $startDate = date("Y-m-d h:i:s",strtotime($icsDate['DTSTART;VALUE=DATE']));
                $endDate = date("Y-m-d h:i:s",strtotime($icsDate['DTEND;VALUE=DATE']));
                $events = $icsDate['SUMMARY'];
                $check_booking_date = PropertyBooking::where(['property_id'=>$request->input('property_listing_id'),'start_date'=>Carbon::parse($startDate),'end_date'=>Carbon::parse($endDate)])->first();
               if(is_null($check_booking_date)):
                $property_booking =  PropertyBooking::create([
                    "property_id" =>$request->input('property_listing_id'),
                    "start_date" =>$startDate,
                    "end_date" =>$endDate,
                    "events" =>$events,
                    "booking_time_stamps" =>$dateTimeStamp
                ]);
               endif;

            endforeach;
            if($property_booking):
                ImportIcal::create([
                    "property_id" =>$request->input('property_listing_id'),
                    'ical_link'=>$request->input('import_calender_url')
                ]);
                return response()->json([
                    'status'=>"1",
                    'msg'=>"Calender Synchronized Successfully !"
                ]);
            else:
                return response()->json([
                    'status'=>"0",
                    'msg'=>"Calender Not Synchronized, Please Try Aagin"
                ]);
            endif;
        }catch (Exception $e) {
            dd("Error:-".$e->getMessage());
        }
    }
    // Calender Syncroniztion Method Start End 

    function getICSDates($key, $subKey, $subValue, $icsDates) {
        if ($key != 0 && $subKey == 0) {
            $icsDates [$key] ["BEGIN"] = $subValue;
        } else {
            $subValueArr = explode ( ":", $subValue, 2 );
            if (isset ( $subValueArr [1] )) {
                $icsDates [$key] [$subValueArr [0]] = $subValueArr [1];
            }
        }
        return $icsDates;
    }


    public function getReviewsRating (Request $request) {
        // dd("test");
        if($request->ajax()):
            $propertyRating =[];
            if($request->input("property_id") !=""):
                $propertyRating = PropertyReviewsRating::select('id','reviews_heading','guest_name','reviews')->where('property_id',$request->input("property_id"))->latest();
            endif;
           return Datatables::of($propertyRating)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm" onclick="editReviewsRating('.$row->id.')">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" onclick="reviewsDelete('.$row->id.')">Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action',])
                ->make(true);
        endif;
    }

    public function storeReviewsRating(Request $request) {
        $propertyReviewsRating = PropertyReviewsRating::create([
            'property_id'=>$request->input('property_listing_id'),
            'reviews_heading'=>$request->input('reviews_heading'),
            'guest_name'=>$request->input('guest_name'),
            'place'=>$request->input('place'),
            'reviews'=>$request->input('reviews'),
            'rating'=>$request->input('rating'),
        ]);
        if($propertyReviewsRating):
            return response()->json([
                'status'=>'1',
                'msg'=>"Reviews added successfully!"
            ]);
        else:
            return response()->json([
                'status'=>'0',
                'msg'=>"Reviews Not added !,Please try Again"
            ]);
        endif;
    }

    public function storeOwnerInformation(Request $request) {
        $originalImageName="";
        if ($request->hasFile('owner_profile_image')):
            $path = storage_path('public/upload/owner_image/');
            if(file_exists($path.$request->input('property_old_image'))):
                unlink($path.$request->input('property_old_image'));
            endif;
            $image = $request->file('owner_profile_image');
            $ext = "webp";
            $convertImage = Image::make($image->getRealPath())->encode($ext,100);
            $originalImageName = uniqid().'.'.$ext;
            Storage::put('public/upload/owner_image/'.$originalImageName, $convertImage);

        endif;
        $propertyListing = PropertyListing::where('id',$request->input('property_listing_id'))->update([
            'owner_first_name'=>$request->input("first_name"),
            'owner_last_name'=>$request->input("last_name"),
            'owner_phone'=>$request->input("phone"),
            'owner_address'=>$request->input("owner_address"),
            'owner_email'=>$request->input("email"),
            'owner_type'=>$request->input("owner_type"),
            'owner_state'=>$request->input("state"),
            'owner_zipcode'=>$request->input("zipcode"),
            'owner_owner_fax'=>$request->input("owner_fax"),
            'owner_profile_image'=>$originalImageName,
            'status'=>'1'
       ]);
       if($propertyListing):
            return response()->json([
                'msg'=>"Property Created Successfully!, Please wait redirecting..",
                'status'=>'1',
                'url'=>route("admin.property.listing.index")
            ]);
        else:
            return response()->json([
                'status'=>'0'
            ]);
        endif;
    }

    // Get Rental Rate Method 
    public function getRentalRates(Request $request) {
        $propertyRates =PropertyRates::where('id',$request->input("id"))->first();
        return response()->json([
            'status'=>'1',
            'data' =>$propertyRates
        ]);
    }

    // Update rental rates Method
    public function UpdateRentalRates(Request $request) {
        $propertyRates = PropertyRates::where('id',$request->input("id"))->update([
            'session_name'=>$request->input("session_name"),
            'from_date'=>$request->input("from_date"),
            'to_date'=>$request->input("to_date"),
            'nightly_rate'=>$request->input("nightly_rate"),
            'weekly_rate'=>$request->input("weekly_rate"),
            'weekend_rates'=>$request->input("weekend_rate"),
            'monthly_rate'=>$request->input("monthly_rate"),
            'minimum_stay'=>$request->input("minimum_stay"),
        ]);
        if($propertyRates):
            return response()->json([
                'msg'=>"Rental Rated Updated Successfully!,",
                'status'=>'1',
        
            ]);
        else:
            return response()->json([
                'msg'=>"Rental Rated Not Updated!,",
                'status'=>'0',
        
            ]);
        endif;
    }

    // Rental Rates Delete method
    public function deleteRentalRates(Request $request) {
        $propertyRates = PropertyRates::findOrFail($request->input('id'))->delete();
        if($propertyRates):
            return response()->json([
                'status'=>'1',
                'msg'=>"Rental Rates Delete Successfully !"
            ]);
        else:
            return response()->json([
                'status'=>'1',
                'msg'=>"Rental Rates Not Delete !"
            ]);
        endif;
    }

    // Property Delete Method
    public function deleteProperty(Request $request) {
        $propertyListing = PropertyListing::where('id',$request->input('id'))->delete();
        if($propertyListing):
            return response()->json([
                'status'=>'1',
                'msg'=>"Property Delete Successfully !"
            ]);
        else:
            return response()->json([
                'status'=>'1',
                'msg'=>"Property Not Delete !"
            ]);
        endif;
    }

    public function propertyApproval(Request $request) {
        $propertListingUpdate = PropertyListing::where('id',$request->input('id'))->update([
            'approval'=>$request->input('value'),
            'subscription_date'=>$request->input('value')=='1'?date('Y-m-d'):NUll,
        ]);
        if($propertListingUpdate):
            return response()->json([
                'status'=>'1',
                'msg'=> $request->input('value')=='1'?"Property Approved Successfully !":"Property Inactive Successfully",
            ]);
        else:
            return response()->json([
                'status'=>'1',
                'msg'=>"Property Not Approved ! Please try again."
            ]);
        endif;
    }

    public function propertyFeature(Request $request) {
        $propertListingUpdate = PropertyListing::where('id',$request->input('id'))->update([
            'feature'=>$request->input('value'),
            'property_feature_expiration_date'=>$request->input('value')==1?NULL:date('Y-m-d h:i:s')
        ]);
        if($propertListingUpdate):
            return response()->json([
                'status'=>'1',
                'msg'=> $request->input('value')=='1'?"Feature Property Added Successfully !":"Feature Property Removed Successfully",
            ]);
        else:
            return response()->json([
                'status'=>'1',
                'msg'=>"Feature Property not added ! Please try again."
            ]);
        endif;
    }

    public function reviewsRatesGet(Request $request){
        $propertyReviewsRating = PropertyReviewsRating::where('id',$request->id)->first();
        if($propertyReviewsRating):
            return response()->json([
                'status'=>'1',
                'data'=>$propertyReviewsRating,
            ]);
        else:
            return response()->json([
                'status'=>'1',
                'msg'=>"Reviews Not found ! Please try again."
            ]);
        endif;

    }
    public function reviewsRatingUpdate(Request $request){
        $PropertyReviewsRatingUpdate = PropertyReviewsRating::where('id',$request->input('id'))->update([
            "reviews_heading"=>$request->input('reviews_heading'),
            "guest_name"=>$request->input("guest_name"),
            "place"=>$request->input("place"),
            "reviews"=>$request->input("reviews"),
            "rating"=>$request->input("rating_update"),
        ]);
        if($PropertyReviewsRatingUpdate):
            return response()->json([
                'status'=>'1',
                'msg'=>"Reviews Update Successfully",
            ]);
        else:
            return response()->json([
                'status'=>'1',
                'msg'=>"Reviews Not Update.! Please try agains",
            ]);
        endif;
    }
    public function reviewsRatingDelete(Request $request) {
        $PropertyReviewsRating = PropertyReviewsRating::where('id',$request->input('id'))->delete();
        if($PropertyReviewsRating):
            return response()->json([
                'status'=>'1',
                'msg'=>"Reviews Rating Delete Successfully !"
            ]);
        else:
            return response()->json([
                'status'=>'1',
                'msg'=>"Review Rating Not Delete !"
            ]);
        endif;
    }

    public function getPropertyEvent (Request $request)  {
        $propertyEvent = PropertyBooking::where('property_id',$request->input('id'))->get();
        $property = []; 
        foreach($propertyEvent as $propertyEvents):
            $data['event_id'] = $propertyEvents->id;
            $data['title'] = $propertyEvents->events??"";
            $data['start'] = $propertyEvents->start_date;
            $data['end'] = $propertyEvents->end_date;
            $data['color']='#FF0000';
            array_push($property ,$data);
        endforeach;

        return response()->json([
            'msg'=>"Event Fetched Successfully",
            'data'=>json_encode($property),
        ]);
    }

    public function deletePropertyImage(Request $request) {
        $propertyGallryImage = PropertyGallery::findOrFail($request->input('id'))->delete();
        if($propertyGallryImage):
            return response()->json([
                'message'=>'Image Delete Successfully'
            ]);
        else:
            return response()->json([
                'status'=>500,
                'message'=>'Images Not Delete, Please Try again',
            ]);
        endif;
    }

    public function getPropertyGalleryImaage(Request $request) {
        $propertyGallryImages = PropertyGallery::where('property_id',$request->input('property_id'))->get();
        $galleryImage = [];
        foreach($propertyGallryImages as $propertyGalleryImage):
            $data = [
                'id'=>$propertyGalleryImage->id,
                'property_id'=>$propertyGalleryImage->property_id,
                'url'=> url('public/storage/upload/property_image/gallery_image/'.$propertyGalleryImage->image_name)
            ];
            $galleryImage[]=$data;
        endforeach;
        return response()->json([
            'data'=>$galleryImage
        ]);
    }

    public function updateGalleryImageOrder(Request $request) {
        foreach($request->input('picsOrder') as $key =>$picId):
            PropertyGallery::where(['property_id'=>$request->input('property_id'),'id'=>$picId])->update([
                'image_order'=>($key+1)
            ]);
        endforeach;
    }

    public function blockManualBooking(Request $request) {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $check_booking_date = PropertyBooking::where('property_id',$request->input('property_id'))->where([['start_date', '>=', date($startDate)], ['end_date', '<=', date($endDate)] ])->get();
        if($check_booking_date->count() > 0):
           return response()->json([
                'status'=>500,
                "msg"=>"Some dates are already booked,Choose available dates only."
           ], 500);
        endif;
        $property_booking =  PropertyBooking::create([
            "property_id" =>$request->input('property_id'),
            "start_date" =>$request->input('start_date'),
            "end_date" =>$request->input('end_date'),
            "events" =>$request->input('customer_name'),
            "booking_time_stamps" =>date('Y-m-d h:i:s'),
            "booking_source"=>"0"
        ]);
        if($property_booking):
            return response()->json([
                'status'=>200,
                "msg"=>"Calender Blocked SuccessFully"
            ]);
        else:
            return response()->json([
                'status'=>500,
                "msg"=>"Calender Not Blocked,Please try again"
            ]);
        endif;
    }
}

