<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\ChangePasswordRequest;

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

    public function changePassword(ChangePasswordRequest $request) {
        if(($request->has('old_password')) && $request->input('old_password') !=null):
            if (!Hash::check($request->input('old_password'), auth()->user()->password)) { 
                return response()->json([
                    'status'=>false,
                    'msg'=>"You're old password does'nt match"
                ]); 
             }
        endif;

        $changePassword = User::find(auth()->user()->id)->update([
            'password'=>Hash::make($request->input('confirm_password')),
            'show_password'=>$request->input('confirm_password')
        ]);
        if(!$changePassword):
            return response()->json([
                'status'=>false,
                'msg'=>"You're password not change,Please try again"
            ]); 
        else:
            return response()->json([
                'status'=>true,
                'msg'=>"You're password change successfully"
            ]); 
        endif;
        
    }


}
