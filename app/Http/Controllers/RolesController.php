<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Validator;
use Redirect;
use Bouncer;
use Silber\Bouncer\Bouncer as BouncerBouncer;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $role =Bouncer::role()->find('1');
        // // dd('allow',$role->disallow('management'));
        // dd($role->getAbilities());

        return view('roles.index');
    }
    public function allRoles()
    {
  
        $model = Roles::query();
        return DataTables::eloquent($model)
        ->filter(function ($query) {
            if (request()->has('name')) {
                $query->where('name', 'like', "%" . request('name') . "%");
            }
        }, true)
        ->addColumn('action', function($row){
            $actionBtn = "";
            // if($row->id != Session::get('id') && $row->id != 1){
                if(Bouncer::can('role_update')){
                    $actionBtn .='<a href="' . route('edit_role', ['id' => $row->id]) . '" class="mr-3"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>';
                }
                if(Bouncer::can('role_delete')){
                $actionBtn .= '<a class="deleteIt" href="' .route('delete_role', ['id' => $row->id]). '"><i class="fas fa-trash-alt mr-2"></i>Delete</a>';
                }
                return $actionBtn;
            // }
        })
        ->rawColumns(['action'])
        ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles|max:255',
        ]);
       foreach($request->permission as $key => $value){  
           Bouncer::allow($request->name)->to($key);
       }  
       return Redirect::route('role_list')->with('success', "Role Created successfuly.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
     
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role =Bouncer::role()->find($id);
        $abilitiesarray=$role->getAbilities()->pluck('name')->toArray();
        return view('roles.edit',compact('role','abilitiesarray'));

        // dd(in_array('inventory_view_on_hands',$abilitiesarray));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ]);      
        $role =Bouncer::role()->find($id);
        $role->name = $request->name;
        $role->title = $request->name;
        $role->save();
        Bouncer::sync($role)->abilities([]);
        foreach($request->permission as $key => $value){
            Bouncer::allow($role)->to($key);         
        }
        return Redirect::route('role_list')->with('success', "Role Updated successfuly.");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Bouncer::role()->find($id)->delete();
        return Redirect::route('role_list')->with('success', "Role Deleted successfuly.");

    }
}
