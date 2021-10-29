<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryLocationTracking as InventoryModel;
use App\Models\InventoryLocation;
use Validator;
use File;
use Session;

class InventoryLocationTrackingController extends Controller
{
    public function index()
    {
        $barcodes = InventoryModel::select('barcode')->groupBy('barcode')->get();
        $inventories = array();
        foreach ($barcodes as $barcode){
            $from_to_query = InventoryModel::select('from', 'to', 'barcode')->where('barcode', '=', $barcode->barcode)->get();
            $location = array();
            foreach ($from_to_query as $k => $from_to_rec){
                if(!in_array($from_to_rec->to,$location) && ($from_to_rec->to != 'Receiving' && $from_to_rec->to != 'Shipping') )
                {
                    $location[$barcode->barcode][] = $from_to_rec->to;
                }
                elseif(!in_array($from_to_rec->from,$location) && ($from_to_rec->from != 'Receiving' && $from_to_rec->from != 'Shipping') )
                {
                    $location[$barcode->barcode][] = $from_to_rec->from;
                }
                $location[$barcode->barcode] = array_unique($location[$barcode->barcode]);
            }

            $eachBarcodeData = array();
            foreach ($location as $k => $v){
                $total_inventory = 0;
                foreach ($v as $k2 => $v2 ){
                    $get_location_sum = DB::table('inventory_location')->where('location', '=', $v2)->sum('count');
                    // echo $get_location_sum;
                    $eachBarcodeData['barcode'] = $k;
                    $eachBarcodeData['locations'][] = array(
                        'location_name' => $v2,
                        'location_sum'  => $get_location_sum,
                    );
                    $total_inventory = $total_inventory + $get_location_sum;
                }
                $eachBarcodeData['total'] = $total_inventory;
            }
            $inventories[] = $eachBarcodeData;
        }     
        // echo "<pre>";
        // print_r($inventories);
        // echo "</pre>"; 
        return view('inventorylist', compact('inventories'));        



    }

    public function getInventoryDetails(Request $request){

        $inventories = InventoryModel::select('users.email', 'inventory_location_tracking.*')->where('barcode', '=', $request->id)->join('users', 'users.id', '=', 'inventory_location_tracking.user_id')->get();

        return view('InventoryDetails', compact('inventories'));
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
        
        if ($request->from != 'Receiving') {
            $FromLocation = new InventoryLocation();
            $FromLocation->barcode = $request->barcode;
            $FromLocation->count = $request->quantity * -1;
            $FromLocation->location = $request->from;
            $FromLocation->inventory_track_id = $Inventory->id;
            $FromLocation->save();
        }
        if ($request->to != 'Shipping') {
            $ToLocation = new InventoryLocation();
            $ToLocation->barcode = $request->barcode;
            $ToLocation->count = $request->quantity ;
            $ToLocation->location = $request->to;
            $ToLocation->inventory_track_id = $Inventory->id;
            $ToLocation->save();
        }

        return response()->json(['success'=>'Inventory Inserted', 'status' => 'success']);
    }

}
