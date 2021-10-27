<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryLocationTracking as InventoryModel;
use Validator;
use File;

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
        $validation = Validator::make($request->all(),[
            'files' => 'required',
        ]);
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
        return response()->json(['success'=>$request->removepath]);
    }
}
