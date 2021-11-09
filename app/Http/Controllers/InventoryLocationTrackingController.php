<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryLocationTracking as InventoryModel;
use App\Models\InventoryLocation;
use App\Models\User;
use App\Models\Items;
use Validator;
use File;
use Session;
use DataTables;
use Redirect;

class InventoryLocationTrackingController extends Controller
{
    public function index(Request $request)
    {
        $InventoryModel = new InventoryModel();
        $tablename = $InventoryModel->getTable();
        $columns = Schema::getColumnListing($tablename);
        $query = InventoryModel::query()->select('barcode');
        $search = $request->search;
        if ($request->search != '') {
            foreach ($columns as $column) {
                if ($column != 'id' && $column != 'user_id' && $column != 'images' && $column != 'created_at' && $column != 'updated_at') {
                    $query->orWhere($column, 'LIKE', '%' . $search . '%');
                }
            }
        }


        $barcodes = $query->groupBy('barcode')->paginate(10);
        if (empty($barcodes)) {
            return response()->json(["error" => 'Barcode not found', 'status' => '404']);
        }
        

        
        $inventories = array(); 
        $items; 
        foreach ($barcodes as $barcode){
            $count = 0;
            $from_to_query = InventoryModel::select('from', 'to', 'barcode')->where('barcode', '=', $barcode->barcode)->limit(3)->get();
            $location = array();
            $items =  Items::select('item_number')->where('productIdentifier', '=', $barcode->barcode)->first(); 
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
                    $get_location_sum = DB::table('inventory_location')->where('location', '=', $v2)->where('barcode', '=', $barcode->barcode)->where('deleted_at', '=', null)->sum('count');
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



            $getAllLocationData = InventoryModel::select('from', 'to', 'barcode')->where('barcode', '=', $barcode->barcode)->where('deleted_at', '=', null)->get();

            $locationData = array();
            foreach ($getAllLocationData as $k => $getlocationData){
                if(!in_array($getlocationData->to,$locationData) && ($getlocationData->to != 'Receiving' && $getlocationData->to != 'Shipping') )
                {
                    $locationData[$barcode->barcode][] = $getlocationData->to;
                }
                elseif(!in_array($getlocationData->from,$locationData) && ($getlocationData->from != 'Receiving' && $getlocationData->from != 'Shipping') )
                {
                    $locationData[$barcode->barcode][] = $getlocationData->from;
                }
                $locationData[$barcode->barcode] = array_unique($locationData[$barcode->barcode]);
            }
            foreach ($locationData as $k => $v){
               foreach($v as $k2 => $from){
                    $LocationDetails =  DB::table('inventory_location')
                         ->select(DB::raw('SUM(`count`) as `quantity`, expiration_date'))
                         ->where('location', '=', $from)
                         ->where('barcode', '=', $barcode->barcode)
                         ->where('deleted_at', '=', null)
                         ->groupBy('expiration_date')
                         ->get();
                    if (!empty($LocationDetails)) {
                        foreach($LocationDetails as $key => $LocationDetail){
                            $eachBarcodeData['locationsData'][$count]['item'] = $items['item_number'];
                            $eachBarcodeData['locationsData'][$count]['name'] = $from;
                            $eachBarcodeData['locationsData'][$count]['count'] = $LocationDetail->quantity;
                            $eachBarcodeData['locationsData'][$count]['expiration'] =$LocationDetail->expiration_date;
                            $count++;
                        }
                    }
               }
            }



            $inventories['data'][] = $eachBarcodeData;

        }     
        $inventories['links'] = $barcodes->links();

        // echo "<pre>";
        // print_r($items);
        // exit();

        return view('inventorylist', compact('inventories'));        



    }
    public function listsearch (Request $request){
        $InventoryModel = new InventoryModel();
        $tablename = $InventoryModel->getTable();
        $columns = Schema::getColumnListing($tablename);
        $query = InventoryModel::query()->select('barcode');
        $search = $request->search;
        if ($request->search != '') {
            foreach ($columns as $column) {
                if ($column != 'id' && $column != 'user_id' && $column != 'images' && $column != 'created_at' && $column != 'updated_at') {
                    $query->orWhere($column, 'LIKE', '%' . $search . '%');
                }
            }
        }


        $barcodes = $query->groupBy('barcode')->paginate(10);
        if (empty($barcodes)) {
            return response()->json(["error" => 'Barcode not found', 'status' => '404']);
        }
        $items;
        $inventories = array();
        foreach ($barcodes as $barcode){
            $count = 0;
            $from_to_query = InventoryModel::select('from', 'to', 'barcode')->where('barcode', '=', $barcode->barcode)->limit(3)->get();
            $location = array();
            $items =  Items::select('item_number')->where('productIdentifier', '=', $barcode->barcode)->first();
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
                    $get_location_sum = DB::table('inventory_location')->where('location', '=', $v2)->where('barcode', '=', $barcode->barcode)->where('deleted_at', '=', null)->sum('count');
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
            $getAllLocationData = InventoryModel::select('from', 'to', 'barcode')->where('barcode', '=', $barcode->barcode)->where('deleted_at', '=', null)->get();

            $locationData = array();
            foreach ($getAllLocationData as $k => $getlocationData){
                if(!in_array($getlocationData->to,$locationData) && ($getlocationData->to != 'Receiving' && $getlocationData->to != 'Shipping') )
                {
                    $locationData[$barcode->barcode][] = $getlocationData->to;
                }
                elseif(!in_array($getlocationData->from,$locationData) && ($getlocationData->from != 'Receiving' && $getlocationData->from != 'Shipping') )
                {
                    $locationData[$barcode->barcode][] = $getlocationData->from;
                }
                $locationData[$barcode->barcode] = array_unique($locationData[$barcode->barcode]);
            }
            foreach ($locationData as $k => $v){
               foreach($v as $k2 => $from){
                    $LocationDetails =  DB::table('inventory_location')
                         ->select(DB::raw('SUM(`count`) as `quantity`, expiration_date'))
                         ->where('location', '=', $from)
                         ->where('barcode', '=', $barcode->barcode)
                         ->where('deleted_at', '=', null)
                         ->groupBy('expiration_date')
                         ->get();
                    if (!empty($LocationDetails)) {
                        foreach($LocationDetails as $key => $LocationDetail){
                            $eachBarcodeData['locationsData'][$count]['item'] = $items['item_number'];
                            $eachBarcodeData['locationsData'][$count]['name'] = $from;
                            $eachBarcodeData['locationsData'][$count]['count'] = $LocationDetail->quantity;
                            $eachBarcodeData['locationsData'][$count]['expiration'] =$LocationDetail->expiration_date;
                            $count++;
                        }
                    }
               }
            }

            $inventories['data'][] = $eachBarcodeData;

        }     
        $barcodes->withPath(route('inventory'));
        $inventories['links'] = $barcodes->links();
        $html = view('response.inventorylist', compact('inventories', 'search'))->render();
        return response()->json(['html'=> $html, 'status' => 'success']);
    }
    public function getInventoryDetailsView(Request $request){
        return view('InventoryDetails');
    }
    public function getInventoryDetails(Request $request){

        // $inventories = InventoryModel::select('users.email', 'inventory_location_tracking.*')->where('barcode', '=', $request->id)->join('users', 'users.id', '=', 'inventory_location_tracking.user_id')->get();

        // return view('InventoryDetails', compact('inventories'));
        // $model = InventoryModel::onlyTrashed()->get();
        // echo count($model);
        // exit();
        $model = InventoryModel::query();
        return DataTables::eloquent($model,)
        ->filter(function ($query) {
            $query->select('inventory_location_tracking.*', 'users.email')->where('barcode', '=', request('barcode'))->join('users', 'users.id', '=', 'inventory_location_tracking.user_id');
            if (request('trash') == 1) {
                $query->onlyTrashed();
            }
            return $query;
        }, true)
        ->addColumn('images_links', function($row){
            
            $images = (!empty($row->images)) ? explode(',', $row->images) : array();
            $actionBtn = '';
            foreach($images as $image){
                $actionBtn.= '<a target="_blank" href="'.asset('uploads/' . $image ).'" >View Image</a>';
            }
            return $actionBtn;
        })
        // ->addColumn('email', function($row){
            
        //     $email = User::select('email')->where( 'id', '=', $row->user_id)->first();
        //     return $email->email;
        // })
        ->addColumn('time', function($row){
            $created_at = $row->created_at;
            $created_at = date('m/d/Y h:i A', strtotime($created_at));      
            $datetime = new \DateTime($created_at);
            $la_time = new \DateTimeZone('America/New_York');
            $datetime->setTimezone($la_time);
            return  $datetime->format('m/d/Y h:i A'); 
        })
        ->addColumn('actions', function($row){
            if (request('trash') != 1) {
                $id = $row->id;
                $trackingDetails = InventoryLocation::select('barcode','id', 'from_id')->where('inventory_track_id', '=', $row->id)->where('location', '=', $row->to)->first();
                $checkIfItemMoveToDiffrentLocation = InventoryLocation::where('from_id', '=', $trackingDetails['id'])->count();
                if ($checkIfItemMoveToDiffrentLocation < 1) {
                    $html = '<a href="'.route('deletemove', ['id' => $id]).'" class="deleteIt"><i class="fas    fa-trash-alt mr-2"></i>Delete</a>';        
                   return $html;        
                }
            }
        })
        ->rawColumns(['images_links', 'actions'])
        ->toJson();
    }
    public function create()
    {
        return view('scanInventory');        
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
            'to' => 'required',
        ]);
        
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                 return response()->json(["error" => $messages, 'status' => 'error']);
            }
        }
        $request->expiration_date = ( $request->expiration_date == 'null') ? null :  $request->expiration_date;

        if ($request->from != 'Receiving' && empty($request->from_id)) {
            return response()->json(["error" => 'From Id required', 'status' => 'error']);
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
            $FromLocation->expiration_date = $request->expiration_date;  
            $FromLocation->from_id = $request->from_id;      
            $FromLocation->save();            
        }
        if ($request->to != 'Shipping') {
            $ToLocation = new InventoryLocation();
            $ToLocation->barcode = $request->barcode;
            $ToLocation->count = $request->quantity ;
            $ToLocation->location = $request->to;
            $ToLocation->inventory_track_id = $Inventory->id;
            $ToLocation->expiration_date = $request->expiration_date; 
            $ToLocation->from_id = $request->from_id;   
            $ToLocation->save();
        }

        return response()->json(['success'=>'Inventory Inserted', 'status' => 'success']);
    }
    public function getlocationbybarcode(Request $request)
    {        
        $validation = Validator::make($request->all(),[
            'barcode' => 'required'
        ]);

        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                 return response()->json(["error" => $messages, 'status' => 'error']);
            }
        }

        $barcode = $request->barcode;        
        $items =  Items::where('productIdentifier', '=', $barcode)->get(); 
        $barcodes = InventoryModel::select('barcode')->where('barcode', '=', $request->barcode )->first();
        if (empty($barcodes)) {
             return response()->json(["error" => 'Barcode not found', 'items' => $items, 'status' => '404']);
        }
            
        $from_to_query = InventoryModel::select('from', 'to', 'barcode')->where('barcode', '=', $barcode)->get();
        $locations = array();
        foreach ($from_to_query as $k => $from_to_rec){
            if(!in_array($from_to_rec->to,$locations) && ($from_to_rec->to != 'Receiving' && $from_to_rec->to != 'Shipping') )
            {
                $locations[] = $from_to_rec->to;
            }
            elseif(!in_array($from_to_rec->from,$locations) && ($from_to_rec->from != 'Receiving' && $from_to_rec->from != 'Shipping') )
            {
                $locations[] = $from_to_rec->from;
            }
            $locations = array_unique($locations);
        }

        foreach($locations as $i => $location){
            $get_location_sum = DB::table('inventory_location')->where('location', '=', $location)->where('deleted_at', '=', null)->sum('count');
            if ($get_location_sum == 0) {
                unset($locations[$i]);
            }
        }

         
       return response()->json(["locations" => $locations, "items" => $items, 'status' => 'success']);

    }
    public function getExiprationDateAndQuantity(Request $request){
        $validation = Validator::make($request->all(),[
            'from' => 'required',
            'barcode' => 'required'
        ]);

        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                 return response()->json(["error" => $messages, 'status' => 'error']);
            }
        }
        $from = $request->from; 
        $barcode = $request->barcode;        
        $barcodes = InventoryModel::select('barcode')->where('barcode', '=', $request->barcode )->first();
        if (empty($barcodes)) {
          return response()->json(["error" => 'Barcode not found', 'status' => '404']);
        }


        $data = array();
        // $getLocationDetails = DB::select("select , SUM(`count`) as `quantity`, expiration_date from `inventory_location` where `location` = '".$from."' and `barcode` = '".$barcode."' group by `expiration_date`");
        $getLocationDetails =  DB::table('inventory_location')
             ->select(DB::raw('SUM(`count`) as `quantity`, id as `from_id`, expiration_date'))
             ->where('location', '=', $from)
             ->where('barcode', '=', $barcode)
             ->where('deleted_at', '=', null)
             ->groupBy('expiration_date')
             ->get();

        if (!empty($getLocationDetails)) {
            foreach($getLocationDetails as $key => $locationDeatail){
                $data[$key]['count'] = $locationDeatail->quantity;
                $data[$key]['expiration'] = $locationDeatail->expiration_date;
                $data[$key]['from_id'] = $locationDeatail->from_id;
            }
        }

       return response()->json(["data" => $data, 'status' => 'success']);

    }

    public function deletemove(Request $request)
    {
        $moves = InventoryModel::where('id', $request->id)->delete();
        $trackmoves = InventoryLocation::where('inventory_track_id', $request->id)->delete();
        if ($moves && $trackmoves) {
            return Redirect::back()->with('success', "Move deleted successfuly.");
        } else {
            return Redirect::back()->with('danger', "Move not found");
        }

    }
}
