<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\UserEdit;
use Pusher\Pusher;


class UsersController extends Controller
{
    
    public function showAllUsers(Request $request)
    {
        $user = User::paginate(5);
        
       
        return response()->json($user);
       
    }

    public function showOneUser($id)
    {
        return response()->json(User::find($id));
    }

    public function create(Request $request)
    {
        $value = $request->all();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password'=>'required',
        ]);
        
        $opts04 = [ "cost" => 15, "salt" => "salteadoususuueyryy28yyGGtttwqtwtt" ];
        $value['password'] = password_hash($request->password, PASSWORD_BCRYPT, $opts04);

        $user = User::create($value);
        

        return response()->json($user, 201);
    }

    public function update($id, Request $request)
    {
        

        $user = User::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'max:255',
            'email' => 'email|max:225|unique:users',
            'role' => 'string',
        ]);

        // Fill user model
        if ($request->name != ''){
            $user->fill([
                'name' => $request->name,
            ]);
        };
        if ($request->email != ''){
            $user->fill([
                'email' => $request->email,
            ]);
        };
        if ($request->role != ''){
            $user->fill([
                'role' => $request->role,
            ]);
        };

        
        // Save user to database
        $user->save();

        event(new UserEdit($user));

        return response()->json($user, 200);
    }

    public function delete($id, Request $request)
    {
        $user = User::findOrFail($id);
        
        
        if ($user != null) {
            $user->fill([
                'deleted_by' => strval($request->id),
            ]);
            
        }
       
        $user->save();
        
         return response($user, 200);
    }


    public function searchText($input){
        $users = User::where('name', 'like', $input.'%')->orWhere('email','like', $input.'%')->orWhere('role', 'like', $input.'%')->orWhere('id', 'like', $input.'%')->paginate(5);
        return $users;
    }


    public function filterUser($field, $value){
        $users = User::where($field, "=", $value)->paginate(5);
        return $users;
    }


    public function deleteUsers(Request $request){
        // return response($request->all());
        foreach ($request->ids as $id) {
            $user = User::findOrFail($id);
        
        
            if ($user != null) {
                $user->fill([
                    'deleted_by' => strval($request->userID),
                ]);
                
            }
           
            $user->save();
        }
        return response('Deleted Successfully', 200);

    }

    public function showUsersSearch(Request $request){
        $user = User::paginate(5);
        
       
        return response()->json($user);
    }


    
}


