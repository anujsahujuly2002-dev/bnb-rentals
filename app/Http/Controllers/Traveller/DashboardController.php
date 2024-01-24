<?php

namespace App\Http\Controllers\Traveller;

use Illuminate\Http\Request;
use App\Models\BookingInformation;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use net\authorize\api\contract\v1 as AnetAPI;
use App\Http\Requests\Owner\CardDetailsRequest;
use App\Models\BookingPaymentTransactionHistory;
use net\authorize\api\constants\ANetEnvironment;
use App\Models\TravelerPaymentTransactionHistory;
use net\authorize\api\controller as AnetController;

class DashboardController extends Controller
{
    public function dashboard() {
        return view('traveller.dashboard');
    }


    public function booking () {
        return view('traveller.booking');
    }

    public function bookingDetails($id) {
        $bookingDetails = BookingInformation::where('id',base64_decode($id))->first();
        return view('traveller.booking-details',compact('bookingDetails'));
    }

    public function payRemainingBalance($id) {
        $totalAmount = BookingInformation::where('id',base64_decode($id))->first()->dues_amount;
        return view('traveller.pay-remaining-balance',compact('totalAmount'));
    }


    public function makeReminingPayment(CardDetailsRequest $request) {
        $bookingInformation =  BookingInformation::where('id',base64_decode($request->input('id')))->first();
        $totalAmount =  BookingInformation::where('id',base64_decode($request->input('id')))->first()->dues_amount;
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));
        $refId = 'ref' . time();
        $cardNumber = preg_replace('/\s+/', '', $request->input('card_number'));
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($request->input('expiry_year') . "-" .$request->input('expiry_month'));
        $creditCard->setCardCode($request->input('cvv_pin'));
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);
        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($totalAmount);
        $transactionRequestType->setPayment($paymentOne);
        $requests = new AnetAPI\CreateTransactionRequest();
        $requests->setMerchantAuthentication($merchantAuthentication);
        $requests->setRefId($refId);
        $requests->setTransactionRequest($transactionRequestType);
        $controller = new AnetController\CreateTransactionController($requests);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        if($response !=null):
            // Check to see if the API request was successfully received and acted upon
            if($response->getMessages()->getResultCode() == "Ok"):
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $message_text = $tresponse->getMessages()[0]->getDescription().", Transaction ID: " .$tresponse->getTransId() ;
                    $msg_type = "success_msg";    
                    $redirectUrl = route('traveller.booking');
                    BookingPaymentTransactionHistory::create([
                        'booking_information_id'=>$bookingInformation->id,
                        'pay_amount'=>$totalAmount,
                        'transaction_id'=>$tresponse->getTransId(),
                        'payment_response'=>json_encode($tresponse),
                        'status'=>'success'
                    ]);
                    BookingInformation::where('id',$bookingInformation->id)->update([
                        'dues_amount'=>$bookingInformation->dues_amount-$totalAmount,
                    ]);
                } else {
                    BookingPaymentTransactionHistory::create([
                        'booking_information_id'=>$bookingInformation->id,
                        'pay_amount'=>$totalAmount,
                        'transaction_id'=>$tresponse->getTransId(),
                        'payment_response'=>json_encode($tresponse),
                        'status'=>'failed'
                    ]);
                    $message_text = 'There were some issue with the payment. Please try again later.';
                    $msg_type = "error_msg";                                    

                    if ($tresponse->getErrors() != null) {
                        $message_text = $tresponse->getErrors()[0]->getErrorText();
                        $msg_type = "error_msg";                                    
                    }
                }
                // Or, print errors if the API request wasn't successful
            else:
                $message_text = 'There were some issue with the payment. Please try again later.';
                $msg_type = "error_msg";                                    
                $tresponse = $response->getTransactionResponse();
                BookingPaymentTransactionHistory::create([
                    'booking_information_id'=>$bookingInformation->id,
                    'pay_amount'=>$totalAmount,
                    'transaction_id'=>$tresponse->getTransId(),
                    'payment_response'=>json_encode($tresponse),
                    'status'=>'failed'
                ]);
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $message_text = $tresponse->getErrors()[0]->getErrorText();
                    $msg_type = "error_msg";                    
                } else {
                    $message_text = $response->getMessages()->getMessage()[0]->getText();
                    $msg_type = "error_msg";
                }  
            endif;
        else:
            $message_text = "No response returned";
            $msg_type = "error_msg";
        endif;
        return response()->json([
            'msg_type'=>$msg_type,
            'url'=>$redirectUrl??"",
            'msg'=>$message_text,
        ]);
    }


    public function bookingTransactionHistories(Request $request) {
        if($request->ajax()):
            $paymentTransaction = BookingPaymentTransactionHistory::when(auth()->user()->roles()->first()->name=='Traveller',function($traveller){
                $traveller->whereHas('bookingInformation',function($bookingInformation){
                    $bookingInformation->where('user_id',auth()->user()->id);
                });
            })->when(auth()->user()->roles()->first()->name=='Owner',function($owner){
                $owner->whereHas('property',function($property){
                    $property->where('user_id',auth()->user()->id);
                });
            })->where('status','success')->get();
            // dd($paymentTransaction);
            return DataTables::of($paymentTransaction)
            ->addIndexColumn()
            ->editColumn('created_at',function($row){
                return date('M dS Y',strtotime($row->created_at));
            })
            ->rawColumns(['paid_amount','action'])
            ->make(true);
        endif;
        return view('traveller.transaction-histories');
    }


    public function switchToHost() {
        auth()->user()->roles()->sync(['2']);
        return redirect()->route('owner.dashboard');
    }
}
