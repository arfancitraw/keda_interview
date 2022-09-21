<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class MessageController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $sender = auth('api')->user();
        $receiv = $request->receiver_id;
        $receiver = User::where('id', $receiv)->first();

        if (!$receiver) {
            return response()->json([
                'status'    => false,
                'message'   => 'Receiver user not found'
            ],404);
        }

        if ($sender->user_type_id == 1 && $receiver->user_type_id == 2) {
            return response()->json([
                'status'    => false,
                'message'   => 'Can`t send messages to different user types'
            ],403);
        }

        if ($sender->id == $receiver->id) {
            return response()->json([
                'status'    => false,
                'message'   => 'Can`t send messages to myself'
            ],402);
        }

        $message = Message::create([
            'sender_id'     => $sender->id,
            'receiver_id'   => $receiv,
            'message'       => $request->message,
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Message sent successfully',
            'detail'    => $message,
        ],200);
    }

    public function getMyMessage()
    {
        $user = auth('api')->user();

        $message   = Message::where('sender_id', $user->id)->first();
        $mymassage = Message::where('sender_id', $user->id)->orderBy('id','desc')->get();

        if (!$message) {
            return response()->json([
                'status'    => false,
                'message'   => 'Message not found'
            ],404);
        }

        return response()->json([
            'status' => true,
            'data'   => $mymassage,
        ],200);   
    }

    public function getAllMessage()
    {
        $user = auth('api')->user();

        $message = Message::orderBy('id','desc')->get();
        
        if ($message->isEmpty()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Message not found'
            ],422);
        }

        return response()->json([
            'status' => true,
            'data'   => $message,
        ],200);  
    }
}
