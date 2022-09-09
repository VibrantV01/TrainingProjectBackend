<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    
    public function showAllUsers(Request $request)
    {
        $user = DB::table('users');
        //filtering
        if(strtolower($request->role) === 'admin'){
            $user = DB::table('users')->where('role', '=', 'admin');
        } elseif(strtolower($request->role) === 'normal'){
            $user = DB::table('users')->where('role','=','normal');
        }
        if(strtolower($request->deleted_by) === 'id'){
            $user = DB::table('users')->where('deleted_by','=', 'id');
        }
        //sorting
        if(strtolower($request->sort) === 'name'){
            $user = DB::table('users')->orderBy('name', 'desc');
        } elseif(strtolower($request->sort) === 'email'){
            $user = DB::table('users')->orderBy('email', 'asc');
        } elseif(strtolower($request->sort) === 'created_at'){
            $user = DB::table('users')->orderBy('created_at', 'desc');
        }
        $user = $user->get();
       
        return response()->json($user);
       
    }

    public function showOneUser($id)
    {
        return response()->json(User::find($id));
    }

    public function create(Request $request)
    {
        $value = $request->all();

        // $this->validate($request, [
        //     'name' => 'required',
        //     'email' => 'required|email|unique:users',
        //     'password'=>'required',
        // ]);
        
        $opts04 = [ "cost" => 15, "salt" => "salteadoususuueyryy28yyGGtttwqtwtt" ];
        $value['password'] = password_hash($request->password, PASSWORD_BCRYPT, $opts04);

        $user = User::create($value);

        return response()->json($user, 201);
    }

    public function update($id, Request $request)
    {
        // $user = User::findOrFail($id);
        // $user->update($request->all());

        // return response()->json($user, 200);
        //dd($id);

        $user = User::findOrFail($id);
        // // Validate the data submitted by user
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|max:255',
        //     'email' => 'required|email|max:225|'. Rule::unique('users')->ignore($user->id),
        // ]);

        // // if fails redirects back with errors
        // if ($validator->fails()) {
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        $this->validate($request, [
            'name' => 'max:255',
            'email' => 'email|max:225|unique:users',
            'password' => 'string',
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
        if ($request->password != ''){
            $user->fill([
                'password' => $request->role,
            ]);
        };

        // $user->fill([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => $request->password,
        //     'role' => $request->role,
        // ]);

        // Save user to database
        $user->save();


        return response()->json($user, 200);
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);
    }


    public function searchText($input){
        $users = User::where('name', 'like', $input.'%')->orWhere('email','like', $input.'%')->orWhere('role', 'like', $input.'%')->orWhere('id', 'like', $input.'%')->get();
        return $users;
    }


    public function filterUser($field, $value){
        $users = User::where($field, "=", $value)->get();
        return $users;
    }
}
