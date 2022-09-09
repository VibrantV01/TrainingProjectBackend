<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Events\TaskAssigned;
use Illuminate\Contract\event\Dispatcher;
use Pusher\Pusher;


class TaskController extends Controller
{

    public function create(Request $request)
    {
        $value = $request->all();

        $this->validate($request, [
            'title' => 'required',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'asigned_to' => 'required',
            'assigned_by' => 'required',
        ]);

        
        $user = User::where('id', '=', $request->asigned_to)->first();
        if ($user === null) {
        // user doesn't exist
        return response()->json('Cannot assign task to a non existent user');
        }
        $task = Task::create($value);
        
        event(new TaskAssigned($user, $task));
        $app_id = '1475061';
        $app_key = '7e4f96f1381b51749e6a';
        $app_secret = 'a4e10289a9792e9865fc';
        $app_cluster = 'ap2';
       $pusher = new Pusher($app_key, $app_secret, $app_id, ['cluster' => $app_cluster]);
        $pusher->trigger('my-channel', 'my-event', array('message' => 'task assigned'));
        return response()->json($task, 201);
        
        
        
    }

    public function update($id, Request $request)
    {
      
        $task = Task::findOrFail($id);
        if ($task->asigned_to != $request->userID){
            return response()->json('You have not been assigned this task');
        }

        
        if ((strtolower($request->status) != 'in-progress' && strtolower($request->status) != 'completed' && strtolower($request->status) != 'assigned' && strtolower($request->status) != 'deleted') ) {

            return response()->json('Status is mandatory to fill, and it can only take four values');
            
        }

       
        $task->fill([
            'status' => $request->status,
            
        ]);
        
        $task->save();
        $app_id = '1475061';
        $app_key = '7e4f96f1381b51749e6a';
        $app_secret = 'a4e10289a9792e9865fc';
        $app_cluster = 'ap2';
       $pusher = new Pusher($app_key, $app_secret, $app_id, ['cluster' => $app_cluster]);
        $pusher->trigger('my-channel', 'my-event', array('message' => 'task updated'));
       

        return response()->json($task, 200);
    }

    public function delete($id, Request $request)
    {
        $task = Task::findOrFail($id);
        
        if ($task['assigned_by'] != $request->userID){
            return response()->json('Oops! You are not the creator of this task');
        }
        if ($task != null) {
            $task->fill([
                'deleted_by' => strval($request->userID),
                'status' => 'deleted',
            ]);
            
        }
       
        $task->save();
        

         return response($task, 200);
    }

    public function edittask($id, Request $request)
    {

        $task = Task::findOrFail($id);
        if ($task['assigned_by'] != $request->userID){
            return response()->json('You have not assigned this task');
        }

        if ($request->description == '' && $request->title == '' && $request->due_date == ''){
            return response()->json('One of the three fields atleast needs to be filled');
        }

        if($request->filled('description') )
        {
            $task->update(['description'=>$request->description]);
        }
        if($request->filled('title') )
        {
            $task->update(['title'=>$request->title]);
        }
        if($request->filled('due_date') )
        {
            $task->update(['due_date'=>$request->due_date]);
        }
        
      

        return response()->json('Updated!');



        
    }



public function showAllTasks($id){

    $firstCondition = [['assigned_by', $id],['deleted_by',null]];
    $secondCondition = [['asigned_to', $id],['deleted_by',null]];

    $tasks = Task::where($firstCondition)->orWhere($secondCondition)->get();

   
    return response()->json($tasks);
}

public function showAllTasksAdmin(Request $request) {
    $tasks = Task::all();
    return response()->json($tasks);
}


public function searchTask($input){
    $tasks = Task::where('title', 'like', $input.'%')->orWhere('description','like', $input.'%')->orWhere('asigned_to', 'like', $input.'%')->orWhere('id', 'like', $input.'%')->orWhere('assigned_by', 'like', $input.'%')->orWhere('status', 'like', $input.'%')->get();
    return $tasks;
}

public function searchtaskuser($input, $id){
    // [['title', 'like', $input.'%'],['assigned_by', $id],['deleted_by',null]]
    // [['description','like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['asigned_to', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['id', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['status', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['assigned_by', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['description','like', $input.'%'], ['asigned_to', $id],['deleted_by',null]]
    // [['title', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]]
    // [['asigned_to', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]]
    // [['id', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]]
    // [['status', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]]
    // [['assigned_by', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]]
    $tasks = Task::where([['title', 'like', $input.'%'],['assigned_by', $id],['deleted_by',null]])->orWhere([['description','like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['asigned_to', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])->orWhere([['id', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['status', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['assigned_by', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])->orWhere([['description','like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])
    ->orWhere([['title', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])->orWhere([['asigned_to', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])
    ->orWhere([['id', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])->orWhere([['status', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])->
    orWhere([['assigned_by', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])->get();


    return $tasks;
}

public function filtertaskadmin($field, $value){
    $task = Task::where($field, '=', $value)->get();
    return $task;
}

public function sorttaskadmin($field, $order){
    $task = Task::orderBy($field, $order)->get();
    return $task;
}


public function filtertask($field, $value, $id){
    $task = Task::where([[$field, '=', $value],['assigned_by', $id],['deleted_by',null]])->orWhere([[$field, '=', $value], ['asigned_to', $id],['deleted_by',null]])->get();
    return $task;
}

public function sorttask($field, $order, $id){
    $firstCondition = [['assigned_by', $id],['deleted_by',null]];
    $secondCondition = [['asigned_to', $id],['deleted_by',null]];

    $tasks = Task::where($firstCondition)->orWhere($secondCondition)->orderBy($field, $order)->get();
    return $tasks;
}

}
