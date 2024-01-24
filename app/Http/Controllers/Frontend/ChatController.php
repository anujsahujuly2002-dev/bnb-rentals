<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Helper\Helper;

class ChatController extends Controller
{
    public function chat() {
        return view('traveller.chat.index');
    }

    public function getUser() {
        $users = User::when(auth()->user()->roles()->first()->name=='Traveller',function($user){
            $user->whereHas('roles',function($roles){
                $roles->where('name','Owner');
            })->whereHas('reciverChat',function($user){
                $user->where('sender_id',auth()->user()->id);
            });
        })->when(auth()->user()->roles()->first()->name=='Owner',function($user){
            $user->whereHas('roles',function($roles){
                $roles->where('name','Traveller');
            })->whereHas('senderChat',function($user){
                $user->where('reciver_id',auth()->user()->id);
            });
        });
        $users=$users->get(['id','name','image']);

        $user = [];
        foreach($users as $us):
            $user []=[
                'id'=>$us->id,
                'name'=>$us->name,
                'image'=>$us->image !=null?url('public/storage/profile_image/'.$us->image):asset('owner-assets/img/agent-1.jpg'),
                'unread_message' =>Helper::getTotalUnreadReciverMesage(auth()->user()->id,$us->id)
            ];
        endforeach; 
        if($users->count()>0):
            return response()->json([
                'status'=>true,
                'msg'=>"user fetched successfully",
                'data'=>$user
            ],);
        else:
            return response()->json([
                'status'=>true,
                'msg'=>"User Does Not exists",
            ],);
        endif;
    }

    public function InsertChat(Request $request) {
        Chat::where(['sender_id'=>$request->input('reciverId'),'reciver_id'=>auth()->user()->id,'status'=>'0'])->update([
            'status'=>'1',
        ]);
        $chats = Chat::create([
            'sender_id'=>auth()->user()->id,
            'reciver_id'=>$request->input('reciverId'),
            'msg'=>$request->input('msg'),
        ]);
        if($chats):
            return response()->json([
                'status'=>true,
            ]);
        else:
            return response()->json([
                'status'=>false
            ]);
        endif;
    }


    public function getChat(Request $request) {
        $groupGetChats = Chat::where(['sender_id'=>auth()->user()->id,'reciver_id'=>$request->id])->orWhere(function($q) use ($request){
            $q->where(['sender_id'=>$request->id,'reciver_id'=>auth()->user()->id]);
        })->groupBy(DB::raw('Date(created_at)'))->get();
        $html = " <ul>";
        foreach($groupGetChats as $Groupchat):
            $dailyAccourdingChat = Chat::where(['sender_id'=>auth()->user()->id,'reciver_id'=>$request->id])->whereDate('created_at',Carbon::parse($Groupchat->created_at)->format('Y-m-d'))->orWhere(function($q) use ($request,$Groupchat ){
                $q->where(['sender_id'=>$request->id,'reciver_id'=>auth()->user()->id])->whereDate('created_at',Carbon::parse($Groupchat->created_at)->format('Y-m-d'));
            })->get();
            if(now()->diffInHours($Groupchat->created_at) >=24):
                $html .= '<li><div class="divider"> <h6>'.Carbon::parse($Groupchat->created_at)->format('d M Y').'</h6> </div> </li>';
            else:
                $html .= '<li><div class="divider"> <h6>Today</h6> </div> </li>';
            endif;
            foreach($dailyAccourdingChat as $key=> $dailyChat):
                if(now()->diffInMinutes($dailyChat->created_at) >=1):
                    $time = Carbon::parse($dailyChat->created_at)->format('H:i');
                else:
                    $time ="Just Now";
                endif;
                if(auth()->user()->id === $dailyChat->sender_id):
                   
                    $html .='<li class="repaly"><p>'.$dailyChat->msg.'</p><span class="time">'.$time.'</span></li>';
                else:
                    if( $dailyChat->status=='0'):
                        $html .= '<li><div class="divider"> <h6>Unread Message</h6> </div> </li>';
                    endif;
                    $html .= '<li class="sender"><p>'.$dailyChat->msg.'</p> <span class="time">'.$time.'</span></li>';
                endif;
            endforeach;
        endforeach;
        $html .='</ul>'; 
        return response()->json([
            'status'=>true,
            'data'=>$html,
        ]);
        
    }
}
