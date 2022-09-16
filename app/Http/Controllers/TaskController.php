<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Events\TaskAssigned;
use App\Events\TaskEditted;
use App\Events\TaskUpdate;
use App\Events\NotificationEvent;
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
            'asigned_to_name' => 'required',
            'assigned_by_name' => 'required',
        ]);

        
        $user = User::where('id', '=', $request->asigned_to)->first();
        if ($user === null) {
        // user doesn't exist
        return response()->json('Cannot assign task to a non existent user');
        }
        $task = Task::create($value);
        $type = "created";
        event(new TaskAssigned($user, $task));
        event(new NotificationEvent($user, $task, $type));
        $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), ['cluster' => 'ap2']);
        $pusher->trigger('my-channel-'.$request->assigned_by, 'my-event', array('message' => 'Task Created'));
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
        $user = User::where('id', '=', $task->assigned_by)->first();
        $type = "updated";
        event(new TaskUpdate($user, $task));
        event(new NotificationEvent($user, $task, $type));
       

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
        
        $user = User::where('id', '=', $task->asigned_to)->first();
        $type = "deleted";
        event(new NotificationEvent($user, $task, $type));
        event(new TaskEditted($user, $task));

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
        $user = User::where('id', '=', $task->asigned_to)->first();
        // dd($user->name);
        $type = "edited";
        event(new TaskEditted($user, $task));

        event(new NotificationEvent($user, $task, $type));
        return response()->json('Updated!');



        
    }



public function showAllTasks($id){

    $firstCondition = [['assigned_by', $id],['deleted_by',null]];
    $secondCondition = [['asigned_to', $id],['deleted_by',null]];

    $tasks = Task::where($firstCondition)->orWhere($secondCondition)->paginate(5);

   
    return response()->json($tasks);
}

public function showAllTasksAdmin(Request $request) {
    $tasks = Task::paginate(5);
    return response()->json($tasks);
}


public function searchTask($input){
    $tasks = Task::where('title', 'like', $input.'%')->orWhere('description','like', $input.'%')->orWhere('asigned_to', 'like', $input.'%')->orWhere('id', 'like', $input.'%')->orWhere('assigned_by', 'like', $input.'%')->orWhere('status', 'like', $input.'%')->paginate(5);
    return $tasks;
}

public function searchtaskuser($input, $id){
    
    $tasks = Task::where([['title', 'like', $input.'%'],['assigned_by', $id],['deleted_by',null]])->orWhere([['description','like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['asigned_to', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])->orWhere([['id', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['status', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['assigned_by', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])->orWhere([['description','like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])
    ->orWhere([['title', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])->orWhere([['asigned_to', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])
    ->orWhere([['id', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])->orWhere([['status', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])->
    orWhere([['assigned_by', 'like', $input.'%'], ['asigned_to', $id],['deleted_by',null]])->paginate(5);


    return $tasks;
}

public function filtertaskadmin($field, $value){
    $task = Task::where($field, '=', $value)->paginate(5);
    return $task;
}

public function sorttaskadmin($field, $order){
    $task = Task::orderBy($field, $order)->paginate(5);
    return $task;
}


public function filtertask($field, $value, $id){
    $task = Task::where([[$field, '=', $value],['assigned_by', $id],['deleted_by',null]])->orWhere([[$field, '=', $value], ['asigned_to', $id],['deleted_by',null]])->paginate(5);
    return $task;
}

public function sorttask($field, $order, $id){
    $firstCondition = [['assigned_by', $id],['deleted_by',null]];
    $secondCondition = [['asigned_to', $id],['deleted_by',null]];

    $tasks = Task::where($firstCondition)->orWhere($secondCondition)->orderBy($field, $order)->paginate(5);
    return $tasks;
}

public function deleteTasks(Request $request){
    // return response($request->all());
    foreach ($request->ids as $id) {
        $task = Task::findOrFail($id);
        
        if ($task != null) {
            $task->fill([
                'deleted_by' => strval($request->userID),
                'status' => 'deleted',
            ]);
            
        }
       
        $task->save();
    }
    return response('Deleted Successfully', 200);

}


public function stats(Request $request){
    $id = $request->id;
    $output['completed'] = Task::where('asigned_to', $id)->where('status', 'completed')->count();
    $output['assigned'] = Task::where('asigned_to', $id)->where('status', 'assigned')->count();
    $output['in-progress'] = Task::where('asigned_to', $id)->where('status', 'in-progress')->count();
    return response()->json($output, 200);
}

public function statsOwner(Request $request){
    $id = $request->id;
    $output['completedto'] = Task::where('asigned_to', $id)->where('status', 'completed')->count();
    $output['assignedto'] = Task::where('asigned_to', $id)->where('status', 'assigned')->count();
    $output['in-progressto'] = Task::where('asigned_to', $id)->where('status', 'in-progress')->count();
    $output['completedby'] = Task::where('assigned_by', $id)->where('status', 'completed')->count();
    $output['assignedby'] = Task::where('assigned_by', $id)->where('status', 'assigned')->count();
    $output['in-progressby'] = Task::where('assigned_by', $id)->where('status', 'in-progress')->count();
    
    return response()->json($output, 200);
}

}
