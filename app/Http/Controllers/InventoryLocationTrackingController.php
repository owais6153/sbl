<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryLocationTracking as InventoryModel;
use App\Models\OnHand;
use Validator;
use File;
use Session;

class InventoryLocationTrackingController extends Controller
{
    public function index()
    {
        $inventories = InventoryModel::all();
        return view('inventorylist', compact('inventories'));        
    }
    public function create()
    {
        return view('createInventory');        
    }
    public function uploadImage(Request $request)
    {

        if($request->TotalFiles > 0)
        {
                
           for ($x = 0; $x < $request->TotalFiles; $x++) 
           {
 
               if ($request->hasFile('files'.$x)) 
               {
                    $file = $request->file('files'.$x);
                    $file_extension = $file->getClientOriginalExtension();
                    if ($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'JPEG' || $file_extension == 'gif') {
                        
                        $path = public_path('uploads');
                        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
                  
                        $name = time() . '-' . $file->getClientOriginalName();

                        if ($request->file('files'.$x)->move(public_path('uploads'), $name)) {
                            $insert[$x] = $name;
                        }
                    }
                    else{
                        return response()->json(["error" => "File not supported", 'status' => 'error']);
                    }
               }
           }
 
       
 
            return response()->json(['success'=>'Ajax Multiple fIle has been uploaded', 'files' => $insert, 'status' => 'success']);
 
          
        }
        else
        {
           return response()->json(["error" => "Please try again.", 'status' => 'error']);
        }
    }
    public function removeImage(Request $request)
    {
        $path = $request->removepath;
   
        unlink(public_path('uploads/' .$path));
        return response()->json(['success'=>'s', 'status' => 'success', 'path' => public_path('uploads/' .$path)]);
    }
    public function saveInventory(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'barcode' => 'required',
            'quantity' => 'required',
            'from' => 'required',
        ]);

        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                 return response()->json(["error" => $messages, 'status' => 'error']);
            }
        }

        $Inventory = new InventoryModel();
        $Inventory->user_id = Session::get('id');
        $Inventory->barcode = $request->barcode;
        $Inventory->quantity = $request->quantity;
        $Inventory->from = $request->from;
        $Inventory->to = $request->to;
        $Inventory->expiration_date = $request->expiration_date;
        $Inventory->pallet_number = $request->pallet_number;
        if (isset($request->images) && !empty($request->images)) {
            $Inventory->images = implode(',', $request->images);
        }
        $Inventory->save();
        return response()->json(['success'=>'Inventory Inserted', 'status' => 'success']);
    }
    public function inventoryOnhand($value='')
    {

        return view('inventorylistOnhand'); 
    }
    public getOnHandList(){

        $model = OnHand::query();
        return view('userlist', compact('users'));

        return DataTables::eloquent($model)
        ->filter(function ($query) {
            if (request()->has('name')) {
                $query->where('name', 'like', "%" . request('name') . "%");
            }

            if (request()->has('email')) {
                $query->where('email', 'like', "%" . request('email') . "%");
            }
        }, true)
        ->toJson();
    }
}
