<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\City;
use App\Models\State;
use App\Models\Cities;
use App\Models\Region;
use App\Models\Country;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Models\PartnerListing;
use App\Models\PropertyBooking;
use App\Models\PropertyListing;
use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;

class FrontendController extends Controller
{
    public function index() {
        $featuredProperties = PropertyListing::where(['feature'=>'1','approval'=>'1'])->get();
        return view('frontend.index',compact('featuredProperties'));
    }

    public function aboutUs() {
        return view('frontend.pages.about-us');
    }

    public function propertyListing (Request $request) {
        // dd($request->all());
        $regionName=[];
        $cities=[];
        $subCities=[];
        $city_name="";
        $subCityName="";
        $region_name="";
        $types="";
        $type="";
        $state_name = State::where('id',$request->input('state_id'))->first(['id','name']);
        $propertiesTypes = PropertyType::get();
        $regions = Region::where('state_id',$request->input('state_id'))->get();
        $properties = PropertyListing::where('approval','1');
        if($request->input('state_id') !=''):
            $properties = $properties->where('state_id',$request->input('state_id'));
        endif;

        if($request->input('region') !=''):
            $properties = $properties->where('region_id',$request->input('region'));
        endif;
        if($request->input('city') !=''):
            $properties = $properties->where('state_id',$request->input('city'));
        endif;
        if($request->input('sub_city') !=''):
            $properties = $properties->where('sub_city_id',$request->input('sub_city'));
        endif;
        if($request->input('property_type') !=''):
            $properties = $properties->where('property_type_id',$request->input('property_type'));
        endif;
        if($request->input('price_range') !=''):
            $priceRange = explode('-',$request->input('price_range'));
            $properties = $properties->whereBetween('avg_night_rates', [$priceRange[0], $priceRange[1]]);
        endif;
        if($request->input('room') !=''):
            $properties = $properties->where('bedrooms',$request->input('room'));
        endif;
        if($request->input('search') !=''):
            $properties = $properties->where('property_name','like', '%' . $request->input('search') . '%');
        endif;

        if($request->input('type')=='state'):
            $type='region';
        endif;

        // Region  Wise Filter
        if($request->input('type')=='region'):
            $region_name = Region::where('id',$request->input('region'))->first();
            $state_name = State::where('id', $region_name->state_id)->first();
            $type='city';
            $cities = City::where('region_id',$request->input('region'))->get();
            if(count($cities)>0):
                $cities =  $cities;
            else:
                $type='region';
                $regions = $regions;
            endif;
        else:
            $cities = City::where('region_id',$request->input('region'))->get();
        endif;

        // City Wise Filter
        if($request->input('type')=='city'):
            $type='sub_city';
            $region_name = Region::where('id',$request->input('region'))->first();
            $state_name = State::where('id', $region_name->state_id)->first();
            $city_name = City::where('id',$request->input('city'))->first(['id','name']);
            $subCities = Cities::where('city_id',$request->input('city'))->get();
            if(count($subCities)>0):
                $subCities =  $subCities;
            else:
                $type='city';
                $cities = City::where('region_id',$request->input('region'))->get();
            endif;
        endif;

        // Sub city Wise Filter
        if($request->input('type')=='sub_city'):
            $type='sub_city';
            $region_name = Region::where('id',$request->input('region'))->first(['id','name']);
            $city_name = City::where('id',$request->input('city'))->first(['id','name']);
            $subCities = Cities::where('city_id',$request->input('city'))->get();
            $subCityName = Cities::where('id',$request->input('sub_city'))->first(['id','name']);
            // dd($subCityName);
            if(count($subCities)>0):
                $subCities =  $subCities;
            else:
                $type='city';
                $cities = City::where('region_id',$request->input('region'))->get();
            endif;
        endif;
        if ($request->input('destination_or_listing_id') != null && gettype($request->input('destination_or_listing_id')) == "string" && is_numeric($request->destination_or_listing_id) ==false) :
            $destination = explode(',', $request->input('destination_or_listing_id'));
            if (count($destination) == 5) :
                $subCity = Cities::where('name', $destination['0'])->first();
                $country_name = Country::where('id', $subCity->country_id)->first();
                $region_name = Region::where('id', $subCity->region_id)->first();
                $city_name = City::where('id', $subCity->city_id)->first();
                $type = "sub_city";
                $types = 'sub_city';
                $subCities = Cities::where('city_id', $subCity->id)->orderBy('name',"ASC")->get();
                $sub_city_name = Cities::where('id', $subCity->id)->first();
                $state_name = State::where('id', $subCity->state_id)->first();
                if ($subCity != null) :
                    $properties = $properties->where('sub_city_id', $subCity->id);
                endif;
            elseif (count($destination) == 4) :
                $city_name = City::where('name', $destination['0'])->first();
                if ($city_name != null) :
                    $type = "SubCity";
                    $types = 'City';
                    $country_name = Country::where('id', $city_name->country_id)->first();
                    $state_name = State::where('id', $city_name->state_id)->first();
                    $region_name = Region::where('id', $city_name->region_id)->first();
                    $subCities = Cities::where('city_id', $city_name->id)->orderBy('name',"ASC")->get();
                    $properties = $properties->where('city_id', $city_name->id);
                endif;
            elseif (count($destination) == 3) :
                $state = State::where('name', $destination['1'])->first()->id;
                $region_name = Region::where(['name'=> $destination['0'],'state_id'=>$state])->first();
                if ($region_name != null) :
                    $country_name = Country::where('id', $region_name->country_id)->first();
                    $state_name = State::where('id', $region_name->state_id)->first();
                    $cites = City::where('region_id', $region_name->id)->orderBy('name',"ASC")->get();
                    $type = "city";
                    $types = 'Region';
                    $properties = $properties->where('region_id', $region_name->id);
                endif;
            elseif (count($destination) == 2) :
                $state_name = State::where('name', $destination['0'])->first();
                if ($state_name != null) :
                    $country_name = Country::where('id', $state_name->country_id)->first();
                    $regions = Region::where('state_id', $state_name->id)->orderBy('name','ASC')->get();
                    $type = "region";
                    $types = 'State';
                    $properties = $properties->where('state_id', $state_name->id);
                endif;
            elseif (count($destination) == 1) :
                $country = Country::where('name', $destination['0'])->first();
                if ($country != null) :
                    $country_name = Country::where('id', $country->id)->first();
                    $types = "country";
                    $type = 'State';
                    $state = State::where('country_id', $country->id)->orderBy('name','ASC')->get();
                    $properties = $properties->where('country_id', $country->id);
                else :
                    $state_name = State::where('name', $destination['0'])->first();
                    if ($state_name != null) :
                        $country_name = Country::where('id', $state_name->country_id)->first();
                        $regions = Region::where('state_id', $state_name->id)->orderBy('name','ASC')->get();
                        $type = "Region";
                        $types = 'State';
                        $properties = $properties->where('state_id', $state_name->id);
                    else :
                        $region = Region::where('name', $destination['0'])->first();
                        if ($region != null) :
                            $country_name = Country::where('id', $region->country_id)->first();
                            $regions =City::where('region_id', $region->id)->orderBy('name','ASC')->get();
                            $type = "City";
                            $types = 'Regin';
                            $properties = $properties->where('region_id', $region->id);
                        else:
                            $city_name = City::where('name', $destination['0'])->first();
                            // dd($city_name);
                            if ($city_name != null) :
                                $country_name = Country::where('id', $city_name->country_id)->first();
                                $subCities = Cities::where('city_id', $city_name->id)->orderBy('name','ASC') ->get();
                                $state_name = State::where('id', $city_name->state_id)->first();
                                $type = "Subcity";
                                $types = 'City';
                                $properties = $properties->where('city_id', $city_name->id);
                            endif;
                        endif;
                    endif;
                endif;
            endif;
        elseif(is_numeric($request->destination_or_listing_id)):
            return to_route('property.listing.details',encrypt($request->input('destination_or_listing_id')));
        endif;
        if($request->input('check_in') !=null && $request->input('check_out') !=null):
            $propertiesId = PropertyBooking::whereDate('start_date','>=',Carbon::parse($request->input('check_in'))->format('Y-m-d'))->whereDate('end_date','<=',Carbon::parse($request->input('check_out'))->format('Y-m-d'))->get('property_id');
            $propertiesIds = array_map(fn($properties) => $properties->property_id,iterator_to_array($propertiesId));
            $properties = $properties->whereNotIn('id',$propertiesIds);
        endif;
        $properties = $properties->paginate('15');
        return view('frontend.property-listing',compact('propertiesTypes','properties','state_name','regions','type','cities','subCities','region_name','city_name','subCityName','types','type'));
    }

    public function contactUs () {
        return view('frontend.pages.contact-us');
    }

    public function listOurProperty() {
       
        return view("frontend.list-our-property");
    }

    public function destintaionSuggestion(Request $request)
    {
        $wordSuggestion = [];
        $countries =Country::where('name', 'LIKE', strtolower($request->input('value')) . '%')->distinct()->get();
        $states =State::where('name', 'LIKE', strtolower($request->input('value')) . '%')->distinct()->get();
        $regions =Region::where('name', 'LIKE', strtolower($request->input('value')) . '%')->distinct() ->get();
        $cities =City::where('name', 'LIKE', strtolower($request->input('value')) . '%')->distinct()->get();
        $subCities = Cities::where('name', 'LIKE', strtolower($request->input('value')) . '%')->distinct()->get();
        if($countries->count() >0):
            foreach($countries as $country):
                $wordSuggestion []= strlen($country?->name)<=3?strtoupper($country?->name):ucfirst($country?->name);
            endforeach;
        endif;
        if($states->count() >0):
            foreach($states as $state):
                $country = strlen($state->country->name) <=3?strtoupper($state->country->name):ucfirst($state->country->name);
                $wordSuggestion []= ucfirst($state->name).','.$country;
            endforeach;
        endif;
        if($regions->count() >0):
            foreach($regions as $region):
                $country = strlen($region->country->name) <=3?strtoupper($region->country->name):ucfirst($region->country->name);
                $state = ucfirst($region->state->name);
                $wordSuggestion []= ucfirst($region->name).','.$state.','.$country;
            endforeach;
        endif;
        if($cities->count() >0):
            foreach($cities as $city):
                $country = strlen($city->country->name) <=3?strtoupper($city->country->name):ucfirst($city->country->name);
                $state = ucfirst($city->state->name);
                $region = ucfirst($city->region->name);
                $wordSuggestion []= ucfirst($city->name).','.$region.','.$state.','.$country;
            endforeach;
        endif;
        if($subCities->count()>0):
            foreach($subCities as $subCity):
                $country = strlen($subCity->country->name) <=3?strtoupper($subCity->country->name):ucfirst($subCity->country->name);
                $state = ucfirst($subCity->state->name);
                $region = ucfirst($subCity->region->name);
                $city= ucfirst($subCity->city->name);
                $wordSuggestion []= ucfirst($subCity->name).','.$city.','.$region.','.$state.','.$country;
            endforeach;
        endif;
        return response()->json([
            'data' => $wordSuggestion
        ]);
    }


    public function partnerListing(Request $request) {
        $states = State::orderBy('name','ASC')->get();
        $businessCategories = BusinessCategory::get();
        $partnerListings = New PartnerListing();
        /* if($request->input('state')):
            $partnerListings = $partnerListings->where('')
        endif; */
        $partnerListings = $partnerListings->paginate(10);
        return view('frontend.pages.partner-listing',compact('partnerListings','states','businessCategories'));
    }

    public function calender(Request $request) {
        $propertyDetail = PropertyListing::where('id',$request->input('property_id'))->first();
        $data = ['cdate'=>$request->input('date'),'propertyDetail'=>$propertyDetail];
        $view =view('frontend.calender',$data)->render();
        return response()->json([
            'data'=>$view
        ]);
    }
}
