<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function logout(Request $request) {
        $token = auth()->user()->token();
        $token->revoke();
        return response()->json([
            'status' => true,
            'msg' => 'You have been successfully logged out.'
        ], 200);
    }

    public function changeUserType(Request $request){
        if(auth()->user()->roles()->first()->id ==$request->input('role_id'))
        return response()->json([
            'status'=>false,
            'msg'=>'Not Allowed change the role'
        ]);
        auth()->user()->roles()->sync([$request->input('role_id')]);
        return response()->json([
            'status'=>true,
            'msg'=>'Role Changed Sucessfully'
        ]); 
    }


}
