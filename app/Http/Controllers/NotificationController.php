<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct()
    {
       
    }
    public function create(array $data){
        Notification::create($data);
    }
    public function listNotification(Request $request)
    {
         $userId = auth()->user()->id;
         $notif = Notification::query()->where('user_id','=', $userId)->get();
        return response()->json($notif, 200);
    }

    public function deleteNotification($id)
    {
        Notification::findOrFail($id)->delete();
        return response()->json("Deleted Successfully", 200);

    }

    public function clearNotification(Request $request)
    {
        $userId = auth()->user()->id;
        $notif = Notification::where('user_id',$userId)->delete();
        return response()->json("Deleted Successfully", 200);

    }

}
