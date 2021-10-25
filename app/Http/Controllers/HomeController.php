<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Redirect;
use Hash;
use Validator;
use Session;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            return Redirect::route('user_list');
        }
        else{
            return view('index');
        }
        
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            
            $request->session()->regenerate();
            Session::put('id', Auth::user()->getId());

            return redirect::route('user_list');
        }
        else{
            return Redirect::back()->with('danger', array("Credentials not matched."));
        }
    }
    public function logout(Request $request) {
      Auth::logout();
      return redirect('/');
    }
    public function users(){
        $users = User::all();
        return view('userlist', compact('users'));
    }
    public function add_user(){
        return view('adduser');
    }
    public function addusers(Request $request){
        $validation = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'email|unique:users|required',
            'password' => 'required',
        ]);
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;               
            }
            return Redirect::back()->with('danger', $error_array);
        }
        else{
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();


            return Redirect::route('user_list')->with('success', "User added successfuly.");

        }

    }
    public function edit_user(Request $request){
        if( $request->id != Session::get('id') && $request->id != 1 ){
            $user = User::where('id', '=', $request->id)->first();
            if (empty($user)) {
                abort(404);
            }

            return view('edituser', compact('user'));
        }
        else{
            abort(404);
        }
    }
    public function edituser(Request $request){
        if( $request->id != Session::get('id') && $request->id != 1 ){
            $validation = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required',
                'id' => 'required',
            ]);
            if ($validation->fails())
            {
                foreach($validation->messages()->getMessages() as $field_name => $messages)
                {
                    $error_array[] = $messages;               
                }
                return Redirect::back()->with('danger', $error_array);
            }
            else{
                $user = new User;
                $user->exists = true;
                $user->id = $request->id;
                $user->name = $request->name;
                $user->email = $request->email;
                if($request->password != ''){
                    $user->password =   Hash::make($request->password);
                }
                $user->save();


                return Redirect::route('user_list')->with('success', "User updated successfuly.");

            }
        }
        else{
            abort(404);
        }


    }
    public function deleteuser(Request $request)
    {   
        if( $request->id != Session::get('id') && $request->id != 1 ){
            $user = User::where('id', $request->id)->delete();
            if ($user) {
                return Redirect::back()->with('success', "User deleted successfuly.");
            } else {
                return Redirect::back()->with('danger', array("User not found"));
            }
        }
        else{
            abort(404);
        }
    }
}
