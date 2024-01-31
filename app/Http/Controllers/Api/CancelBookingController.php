<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CancellentionReason;
use Illuminate\Http\Request;

class CancelBookingController extends Controller
{
    public function cancellentionReasonList() {
        $cancellentionReasons = CancellentionReason::get(['id','name']);
        return response()->json([
            "status"=>true,
            'msg'=>"Cancellention Reason Fetched Successfully",
            'data'=>$cancellentionReasons
        ]);
    }
}
