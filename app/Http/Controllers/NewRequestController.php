<?php

namespace App\Http\Controllers;

use DB;
use App\Models\BusinessPlan;
use App\Models\Chatroom;
use App\Models\ChatroomUser;
use App\Models\Message;
use App\Models\NewRequest;
use Illuminate\Http\Request;

class NewRequestController extends Controller
{
    public function new($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        
        $businessPlan = BusinessPlan::where("unique_id", $unique_id)->first();
        if(!$businessPlan){
            return back();
        }else{
            return view('login.request.new')->with('requestBusiness', json_encode($businessPlan));
        }
    }

    public function view(){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }

        $newRequest = DB::select("SELECT u.name as requesterName, u.id as requesterId, bp.name as companyName,
                                            req.title, req.details, req.unique_id, req.status
                                        FROM request req 
                                        JOIN user u ON req.user_id = u.id AND u.status = 1
                                        LEFT JOIN business_user bu ON u.id = bu.user_id AND bu.status = 1
                                        LEFT JOIN business_plan bp ON bu.business_plan_id = bp.id AND bp.status = 1
                                        WHERE (req.user_id = :myId OR req.business_plan_id = :businessPlanId )
                                            AND (req.status = 1 OR req.status = 2)
                                        ORDER BY req.status desc, req.create_at desc
                                        ", ['myId' => \getMyId(), 'businessPlanId' => \getMyBusinessPlanId()]);

        $myId = \getMyId();
        foreach ($newRequest as $request) {
            if($request->requesterId == $myId){
                $request->requesterName .= " (You)";
            }
            unset($request->requesterId);
        }

        return view('login.request.view')->with('newRequest', json_encode($newRequest));
    }

    public function backendResponse($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $myId = \getMyId();

        $newRequest = NewRequest::where('unique_id', $unique_id)->first();
        $participant = ChatroomUser::where('chatroom_id', $newRequest->chatroom_id)
                                    ->where('user_id', $myId)
                                    ->first();

        // check if requester is myself and not in chat room
        if($newRequest->user_id != $myId && !$participant){
            $addMe = new ChatroomUser;
            $addMe->chatroom_id = $newRequest->chatroom_id;
            $addMe->user_id = $myId;
            $addMe->side = '0';
            $addMe->save();
        }
        if($newRequest->status == 2){
            // first response
            $newRequest->status = 1;
            $newRequest->save();
            return redirect()->route('login.chatroom.chat', ['uniqueId'=> $newRequest->unique_id]);
        }else{
            return redirect()->route('login.chatroom.chat', ['uniqueId'=> $newRequest->unique_id]);
        }
    }

    public function ajaxNewRequest(Request $request){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->only('unique_id', 'title', 'details');
        $businessPlan = BusinessPlan::select('id', 'name')->where('unique_id', $input['unique_id'])->first();
        
        if($businessPlan->id == \getMyBusinessPlanId()){
            $title = $input['title'];
        }else{
            $title = $input['title'] . " - " . $businessPlan->name;
        }

        $chatroom = new Chatroom;
        $chatroom->unique_id = \getUniqid();
        $chatroom->name = $title;
        $chatroom->description = $input['details'];
        $chatroom->type = 'Channel';
        $chatroom->save();
        
        $newRequest = new NewRequest;
        $newRequest->unique_id = $chatroom->unique_id;
        $newRequest->chatroom_id = $chatroom->id;
        $newRequest->user_id = \getMyId();
        $newRequest->business_plan_id = $businessPlan->id;
        $newRequest->title = $title;
        $newRequest->details = $input['details'];
        $newRequest->save();

        $addMe = new ChatroomUser;
        $addMe->chatroom_id = $chatroom->id;
        $addMe->user_id = \getMyId();
        $addMe->side = '1';
        $addMe->save();

        $message = new Message;
        $message->unique_id = \getUniqid();
        $message->user_id = \getMyId();
        $message->chatroom_id = $chatroom->id;
        $message->content = $input['details'];
        $message->save();


        $output['result'] = "true";
        $output['redirect'] = route('login.chatroom.chat', ["unique_id" => $chatroom->unique_id]);

        return response()->json(compact('output'));
    }
}
