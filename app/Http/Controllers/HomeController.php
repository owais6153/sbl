<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Redirect;
use Hash;
use Validator;
use Session;
use Bouncer;
use DataTables;
use Silber\Bouncer\Bouncer as BouncerBouncer;

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

            return redirect::route('dashboard');
            
        }
        else{
            return Redirect::back()->with('danger', array("Credentials not matched."));
        }
    }
    public function logout(Request $request) {
      Auth::logout();
      return redirect('/');
    }
    public function dashboard(){
        return view('dashboard');
    }
    public function users(){
       
        return view('userlist');
    }
    public function userDisplay()
    {
        $model = User::query();

        return DataTables::eloquent($model)
        ->filter(function ($query) {
            if (request()->has('name')) {
                $query->where('name', 'like', "%" . request('name') . "%");
            }

            if (request()->has('email')) {
                $query->where('email', 'like', "%" . request('email') . "%");
            }
        }, true)
        ->addColumn('action', function($row){

            if($row->id != Session::get('id') && $row->id != 1){
                $actionBtn= '';
                if(Bouncer::can('user_update')){
                    $actionBtn .='<a href="' . route('edit_user', ['id' => $row->id]) . '" class="mr-3"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>';
                }
                if(Bouncer::can('user_delete')){
                $actionBtn .= '<a class="deleteIt" href="' .route('deleteuser', ['id' => $row->id]). '"><i class="fas fa-trash-alt mr-2"></i>Delete</a>';
                }
                // $actionBtn= '<a href="' . route('edit_user', ['id' => $row->id]) . '" class="mr-3"><i class="fas fa-pencil-alt mr-2"></i>Edit</a> <a class="deleteIt" href="' .route('deleteuser', ['id' => $row->id]). '"><i class="fas fa-trash-alt mr-2"></i>Delete</a>';
                return $actionBtn;
            }
        })
        ->rawColumns(['action'])

        ->toJson();
    }
    public function add_user(){
       
        $roles = Bouncer::role()->all()->pluck('name');
       
        return view('adduser',compact('roles'));
    }
    public function addusers(Request $request){
        $validation = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'email|unique:users|required',
            'password' => 'required',
            'role'=>'required'
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
            $user->assign($request->role);
            return Redirect::route('user_list')->with('success', "User added successfuly.");

        }

    }
    public function edit_user(Request $request){
        if( $request->id != Session::get('id') && $request->id != 1 ){
            $user = User::where('id', '=', $request->id)->first();
            $roles = Bouncer::role()->all()->pluck('name');
            
            if (empty($user)) {
                abort(404);
            }

            return view('edituser', compact('user','roles'));
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
                $currentRole= $user->getRoles();
                
                if(empty($currentRole[0])){
                    $user->assign($request->role);
                }else{
                    if($currentRole[0] != $request->role){
                        $user->retract($currentRole[0]);
                        $user->assign($request->role);
                    }
                }
                

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
