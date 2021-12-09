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

    public function filterAllLocations($from_to_query, $barcode, $unique = true){
        $location = array();
        foreach ($from_to_query as $k => $from_to_rec){
            if(!in_array($from_to_rec->to,$location) && (strtolower($from_to_rec->to) != 'receiving' && strtolower($from_to_rec->to) != 'shipping'  && strtolower($from_to_rec->to) != 'production' && strtolower($from_to_rec->to) != 'adjustment' ) )
            {
                $location[$barcode][] = $from_to_rec->to;
            }
            if(!in_array($from_to_rec->from,$location) && (strtolower($from_to_rec->from) != 'receiving' && strtolower($from_to_rec->from) != 'shipping'  && strtolower($from_to_rec->from) != 'production' && strtolower($from_to_rec->from) != 'adjustment') )
            {
                $location[$barcode][] = $from_to_rec->from;
            }

            // Unique the locations to prevent duplications
            // Use barcode as key to prevent barcode duplication
            if($unique && !empty($location)){
                $location[$barcode] = array_unique($location[$barcode]);
            }
        }
        return $location;
    }



    public function getDataByItems(Request $request)
    {
        // Search for in all columns
        $InventoryModel = new InventoryModel();
        $tablename = $InventoryModel->getTable();
        $columns = Schema::getColumnListing($tablename);
        $query = InventoryModel::query()->select('barcode', 'item_id');
        $search = $request->search;
        if ($request->search != '') {
            
            
            $searchitems =  Items::where('item.item_number', 'LIKE','%' . $search. '%')->get()->pluck('id')->toArray();
           
            if(!empty($searchitems)){
        
            
                $query->whereIn('item_id', $searchitems);
            }
            else{
                foreach ($columns as $column) {
                    if ($column != 'id' && $column != 'user_id' && $column != 'images' && $column != 'created_at' && $column != 'updated_at') {
                        $query->orWhere($column, 'LIKE', '%' . $search . '%');
                    }
                }
            }
        }
        
        


        //Getting all barcodes         
        $barcodes = $query->groupBy('item_id')->paginate(10);
                
        if (empty($barcodes)) {
            return response()->json(["error" => 'Barcode not found', 'status' => '404']);
        }
        

        
        $inventories = $from_to_query = array(); 
        $items; 
        
        // Getting data foreach barcode        
        foreach ($barcodes as $barcode){
            $count = 0;
            $locations = array();   
            // Getting item against barcode
            $items =  Items::select('item.item_number', 'item.id','item.ridgefield_onhand')->join('item_identifiers', 'item.id', '=', 'item_identifiers.item_id')->where('item.id', '=', $barcode->item_id)->first();
            
            // Get all locations against this barcode
            $from_to_query = InventoryModel::select('from', 'to', 'barcode')->where('item_id', '=', $barcode->item_id)->whereRaw("LOWER(`to`) != 'shipping' and LOWER(`to`) != 'production' and LOWER(`to`) != 'adjustment'")->get();

             
            $locations = $this->filterAllLocations($from_to_query, $barcode->barcode, true);

          

            $eachBarcodeData = array();
            
            // Now looping through each location
            foreach ($locations as $barcode_as_key => $location_Array){
                 $eachBarcodeData['locations'] = array();
                $total_inventory = 0;
                $acount = 0;
                foreach ($location_Array as $location_key => $final_location ){
                    // Get specific location detail against barcode 
                    $LocationDetails =  DB::table('inventory_location')
                         ->select(DB::raw('SUM(`count`) as `quantity`, expiration_date'))
                         ->where('location', '=', $final_location)
                         ->where('item_id', '=', $barcode->item_id)
                         ->where('deleted_at', '=', null)
                        //  ->groupBy('expiration_date')
                         ->get();                 
                    $eachBarcodeData['barcode'] = $barcode_as_key;
                
                    if (!isset($eachBarcodeData['item'])) {
                        $eachBarcodeData['item'][] = (isset($items->item_number)) ? $items->item_number : 'Not Found';                            
                    }

                    //getting on hand ridgefield data 
                    if (!isset($eachBarcodeData['onhand'])) {
                        $eachBarcodeData['onhand'] = (isset($items->ridgefield_onhand)) ? $items->ridgefield_onhand : '0';                            
                    }
                    
                    if (!isset($eachBarcodeData['item'])) {
                         $eachBarcodeData['item'][] = 'Not Found';
                    }

                    if (!empty($LocationDetails)) {
                        foreach($LocationDetails as $key => $LocationDetail){                        
                            if ($LocationDetail->quantity  > 0) {
                                // Dont display location more then 3
                                if ($acount < 3) {
                                    $eachBarcodeData['locations'][] = array(
                                        'location_name' => $final_location,
                                        'location_sum'  => $LocationDetail->quantity,
                                    );
                                    
                                }
                                else if($acount == 3){
                                    $eachBarcodeData['more'] = true;
                                }
                                $acount++;    

                                // Get total inventory by adding quantity of each location
                                $total_inventory = $total_inventory + $LocationDetail->quantity;

                                
                                
                            }
                        }
                    }
                    // Getting all barcode by item_id
                    $barcodesForInnerTable = InventoryLocation::select('barcode')->where('item_id', '=', $barcode->item_id)->where('location', '=', $final_location)->groupBy('barcode')->get();
                    // Looping through each barcode
                    foreach ($barcodesForInnerTable as $eachTableRow){
                         // Getting location details
                         $LocationDetails =  DB::table('inventory_location')
                         ->select(DB::raw('SUM(`count`) as `quantity`, expiration_date'))
                         ->where('location', '=', $final_location)
                         ->where('barcode', '=', $eachTableRow->barcode)
                         ->where('item_id', '=', $barcode->item_id)
                         ->where('deleted_at', '=', null)
                        //  ->groupBy('expiration_date')
                         ->get();         

                         // Putting data in array
                        foreach($LocationDetails as $key => $LocationDetail){                
                            if ($LocationDetail->quantity  > 0) {
                                 $eachBarcodeData['locationsData'][$count]['barcode'] = $eachTableRow->barcode; $eachBarcodeData['locationsData'][$count]['name'] = $final_location;
                                $eachBarcodeData['locationsData'][$count]['count'] = $LocationDetail->quantity;
                                $eachBarcodeData['locationsData'][$count]['expiration'] =$LocationDetail->expiration_date;
                                $count++;
                            }
                        }

                    }




                    
                }
                $eachBarcodeData['total'] = $total_inventory;
                //      $eachBarcodeData['diference'] = 0;
                // if($eachBarcodeData['onhand'] > 0){
                    $eachBarcodeData['diference'] =$eachBarcodeData['onhand']-$total_inventory;
                // }

            }

            if(!empty($eachBarcodeData))
                $inventories['data'][] = $eachBarcodeData;

        }     
        

        // echo "<pre>";
        // print_r($inventories);
        // exit();
      




        // this sort data according to Item Name 
        
        if(isset($request->sort) && $request->sort == 'byItemName'){
            $itemnames =array();

            foreach($inventories['data'] as $key=>$items){
                $itemnames[$key] = $items['item'][0];
            }
            if($request->order == 'asc'){
                array_multisort($itemnames, SORT_ASC, $inventories['data']);
            }else{
                array_multisort($itemnames, SORT_DESC, $inventories['data']);
            }
        }


        // this sort data according to Difference

        if(isset($request->sort) && $request->sort == 'byDifference'){
            $itemTotalInventroy =array();

            foreach($inventories['data'] as $key=>$items){
                $itemTotalInventroy[$key] = $items['diference'];
            }
            if($request->order == 'asc'){
                array_multisort($itemTotalInventroy, SORT_ASC, $inventories['data']);
            }else{
                array_multisort($itemTotalInventroy, SORT_DESC, $inventories['data']);
            }
        }

        // this sort data according to total inventary or total qunatity

        if(isset($request->sort) && $request->sort == 'byTotalInventory'){
            $itemTotalInventroy =array();

            foreach($inventories['data'] as $key=>$items){
                $itemTotalInventroy[$key] = $items['total'];
            }
            if($request->order == 'asc'){
                array_multisort($itemTotalInventroy, SORT_ASC, $inventories['data']);
            }else{
                array_multisort($itemTotalInventroy, SORT_DESC, $inventories['data']);
            }
        }
        // this sort data according to On hand 

        if(isset($request->sort) && $request->sort == 'byonHand'){
            $itemTotalInventroy = array();

            foreach($inventories['data'] as $key=>$items){
                $itemTotalInventroy[$key] = $items['onhand'];
            }
            if($request->order == 'asc'){
                array_multisort($itemTotalInventroy, SORT_ASC, $inventories['data']);
            }else{
                array_multisort($itemTotalInventroy, SORT_DESC, $inventories['data']);
            }
        }
        
        

        $filter = 'item';
        if (isset($request->output) && $request->output = 'html') {
          $barcodes->withPath(route('inventory'));
        }

        $inventories['links'] = $barcodes->links();
        if (isset($request->output) && $request->output = 'html') {
            $html = view('response.inventorylist', compact('inventories', 'search'))->render();
            return response()->json(['html'=> $html, 'status' => 'success']);
        }
        else{
            return view('inventorylist', compact('inventories', 'filter'));        
        }


    }




    public function index(Request $request)
    {
        // Search for in all columns
        $InventoryModel = new InventoryModel();
        $tablename = $InventoryModel->getTable();
        $columns = Schema::getColumnListing($tablename);
        $query = InventoryModel::query()->select('barcode');
        $search = $request->search;
        if ($request->search != '') {
            $searchitems =  Items::where('item.item_number', 'LIKE','%' . $search. '%')->get()->pluck('id')->toArray();
    
            if(!empty($searchitems)){
                $query->whereIn('item_id', $searchitems);
            }
            else{
                foreach ($columns as $column) {
                    if ($column != 'id' && $column != 'user_id' && $column != 'images' && $column != 'created_at' && $column != 'updated_at') {
                        $query->orWhere($column, 'LIKE', '%' . $search . '%');
                    }
                }
            }
        }

        //Getting all barcodes         
        $barcodes = $query->groupBy('barcode')->paginate(10);
        if (empty($barcodes)) {
            return response()->json(["error" => 'Barcode not found', 'status' => '404']);
        }
        

        
        $inventories = $from_to_query = array(); 
        $items; 
        
        // Getting data foreach barcode        
        foreach ($barcodes as $barcode){
            $count = 0;
            $locations = array();   

            // Getting item against barcode
            $items =  Items::select('item.item_number', 'item.id', 'item.ridgefield_onhand')->join('item_identifiers', 'item.id', '=', 'item_identifiers.item_id')->where('item_identifiers.productIdentifier', '=', $barcode->barcode)->orWhere('item_identifiers.productIdentifier', '=', '0' . $barcode->barcode)->orWhere('item_identifiers.productIdentifier', '=',  substr($barcode->barcode, 1))->first();
          
            // Get all locations against this barcode
            $from_to_query = InventoryModel::select('from', 'to', 'barcode')->where('barcode', '=', $barcode->barcode)->whereRaw("LOWER(`to`) != 'shipping' and LOWER(`to`) != 'production' and LOWER(`to`) != 'adjustment'")->get();

     
            $locations = $this->filterAllLocations($from_to_query, $barcode->barcode, true);


            $eachBarcodeData = array();
           
            
            // Now looping through each location
            foreach ($locations as $barcode_as_key => $location_Array){
                $eachBarcodeData['locations'] = array();
                $total_inventory = 0;
                $acount = 0;
                foreach ($location_Array as $location_key => $final_location ){
                    // Get quantity against barcode in each location
                    $LocationDetails =  DB::table('inventory_location')
                         ->select(DB::raw('SUM(`count`) as `quantity`, expiration_date'))
                         ->where('location', '=', $final_location)
                         ->where('barcode', '=', $barcode->barcode)
                         ->where('deleted_at', '=', null)
                        //  ->groupBy('expiration_date')
                         ->get();                 
                    $eachBarcodeData['barcode'] = $barcode_as_key;
                    
                    if (!isset($eachBarcodeData['item'])) {
                        $eachBarcodeData['item'][] = (isset($items->item_number)) ? $items->item_number : 'Not Found';                            
                    }

                    //getting on hand ridgefield data 
                    if (!isset($eachBarcodeData['onhand'])) {
                        $eachBarcodeData['onhand'] = (isset($items->ridgefield_onhand)) ? $items->ridgefield_onhand : '0';                            
                    }
                    
                    if (!isset($eachBarcodeData['item'])) {
                         $eachBarcodeData['item'][] = 'Not Found';
                    }

                    if (!empty($LocationDetails)) {
                        foreach($LocationDetails as $key => $LocationDetail){                        
                            if ($LocationDetail->quantity  > 0) {
                                // Dont display location more then 3
                                if ($acount < 3) {
                                    $eachBarcodeData['locations'][] = array(
                                        'location_name' => $final_location,
                                        'location_sum'  => $LocationDetail->quantity,
                                    );
                                    
                                }
                                else if($acount == 3){
                                    $eachBarcodeData['more'] = true;
                                }
                                $acount++;    

                                // Get total inventory by adding quantity of each location
                                $total_inventory = $total_inventory + $LocationDetail->quantity;
                                $eachBarcodeData['locationsData'][$count]['barcode'] = $barcode_as_key;
                                $eachBarcodeData['locationsData'][$count]['name'] = $final_location;
                                $eachBarcodeData['locationsData'][$count]['count'] = $LocationDetail->quantity;
                                $eachBarcodeData['locationsData'][$count]['expiration'] =$LocationDetail->expiration_date;
                                $count++;
                            }
                        }
                    }
                }
                $eachBarcodeData['total'] = $total_inventory;
                $eachBarcodeData['total'] = $total_inventory;
                //      $eachBarcodeData['diference'] = 0;
                // if($eachBarcodeData['onhand'] > 0){
                    $eachBarcodeData['diference'] =  $eachBarcodeData['onhand']-$total_inventory ;
                // }
            }
            
            if(!empty($eachBarcodeData))
                $inventories['data'][] = $eachBarcodeData;

        }     
        

         // this sort data according to Item Name 
        
        if(isset($request->sort) && $request->sort == 'byItemName'){
            $itemnames =array();

            foreach($inventories['data'] as $key=>$items){
                $itemnames[$key] = $items['item'][0];
            }
            if($request->order == 'asc'){
                array_multisort($itemnames, SORT_ASC, $inventories['data']);
            }else{
                array_multisort($itemnames, SORT_DESC, $inventories['data']);
            }
        }

        // this sort data according to total inventary or total qunatity

        if(isset($request->sort) && $request->sort == 'byTotalInventory'){
            $itemTotalInventroy =array();

            foreach($inventories['data'] as $key=>$items){
                $itemTotalInventroy[$key] = $items['total'];
            }
            if($request->order == 'asc'){
                array_multisort($itemTotalInventroy, SORT_ASC, $inventories['data']);
            }else{
                array_multisort($itemTotalInventroy, SORT_DESC, $inventories['data']);
            }
        }
        
          // this sort data according to Difference

        if(isset($request->sort) && $request->sort == 'byDifference'){
            $itemTotalInventroy =array();

            foreach($inventories['data'] as $key=>$items){
                $itemTotalInventroy[$key] = $items['diference'];
            }
            if($request->order == 'asc'){
                array_multisort($itemTotalInventroy, SORT_ASC, $inventories['data']);
            }else{
                array_multisort($itemTotalInventroy, SORT_DESC, $inventories['data']);
            }
        }

        // this sort data according to On hand 

        if(isset($request->sort) && $request->sort == 'byonHand'){
            $itemTotalInventroy = array();

            foreach($inventories['data'] as $key=>$items){
                $itemTotalInventroy[$key] = $items['onhand'];
            }
            if($request->order == 'asc'){
                array_multisort($itemTotalInventroy, SORT_ASC, $inventories['data']);
            }else{
                array_multisort($itemTotalInventroy, SORT_DESC, $inventories['data']);
            }
        }
        

        if (isset($request->output) && $request->output = 'html') {
          $barcodes->withPath(route('inventory'));
        }
        $filter = 'barcode';
        $inventories['links'] = $barcodes->links();
        if (isset($request->output) && $request->output = 'html') {
            $html = view('response.inventorylist', compact('inventories', 'search'))->render();
            return response()->json(['html'=> $html, 'status' => 'success']);
        }
        else{
            return view('inventorylist', compact('inventories', 'filter'));        
        }


    }

    public function getInventoryDetailsView(Request $request){
        return view('InventoryDetails');
    }
    public function getInventoryDetails(Request $request){
        $model = InventoryModel::query();
        return DataTables::eloquent($model)
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
        ->addColumn('email', function($row){
            
            $email = User::select('email')->where( 'id', '=', $row->user_id)->first();
            return $email->email;
        })
        ->addColumn('time', function($row){
            $created_at = $row->created_at;
            $created_at = date('m/d/Y g:i:s A', strtotime($created_at));      
            $datetime = new \DateTime($created_at);
            $la_time = new \DateTimeZone('America/New_York');
            $datetime->setTimezone($la_time);
            return  $datetime->format('m/d/Y g:i:s A'); 
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


        $request->from = (strtolower($request->from) == 'receiving' || strtolower($request->from) == 'adjustment') ? strtolower($request->from) : $request->from;
        $request->to = (strtolower($request->to) == 'shipping' || strtolower($request->to) == 'production' || strtolower($request->to) == 'adjustment') ? strtolower($request->to) : $request->to;
        

        if ($request->from != 'receiving' && $request->from != 'adjustment' && empty($request->from_id)) {
            return response()->json(["error" => 'From Id required', 'status' => 'error']);
        }
        
        if($request->from != 'receiving' && $request->from != 'adjustment'){
            $checkFrom = ($request->from == 'adjustment') ? $request->to : $request->from;
            $LocationDetails = DB::table('inventory_location')
                     ->select(DB::raw('SUM(`count`) as qty'))
                     ->where('location', '=', $checkFrom)
                     ->where('barcode', '=', $request->barcode)
                    //  ->where('expiration_date', '=', $request->expiration_date)
                     ->where('deleted_at', '=', null)
                    //  ->groupBy('expiration_date')
                     ->first();
            if (!empty($LocationDetails)) {
                if($LocationDetails->qty < $request->quantity){
                    return response()->json(["error" => $request->from . " doesn't have enough items.", 'status' => 'error']);
                }
            }
            else{
                return response()->json(["error" => $request->from . " not found in database.", 'status' => 'error']);
            }
        }
                         
                         
        // All moves   
        $Inventory = new InventoryModel();
        $Inventory->user_id = Session::get('id');
        $Inventory->barcode = $request->barcode;
        $Inventory->quantity = $request->quantity;
        $Inventory->from = $request->from;
        $Inventory->to = $request->to;
        $Inventory->expiration_date = $request->expiration_date;
        $Inventory->pallet_number = $request->pallet_number;
        $Inventory->item_id = (isset($request->item_id)) ? $request->item_id : null;
        if (isset($request->images) && !empty($request->images)) {
            $Inventory->images = implode(',', $request->images);
        }
        $Inventory->save();
        
        if ($request->from != 'receiving' && $request->from != 'adjustment') {
            // When Moving From Location (Not Receiving)
            $FromLocation = new InventoryLocation();
            $FromLocation->barcode = $request->barcode;
            $FromLocation->count = $request->quantity * -1;
            $FromLocation->location = $request->from ;
            $FromLocation->inventory_track_id = $Inventory->id;    
            $FromLocation->expiration_date = $request->expiration_date;  
            $FromLocation->item_id = (isset($request->item_id)) ? $request->item_id : null;  
            $FromLocation->from_id = $request->from_id;      
            $FromLocation->save();
        }
        if($request->from == 'receiving' || $request->from == 'adjustment'){
             // If Receiving
            $newToLocation = new InventoryLocation();
            $newToLocation->barcode = $request->barcode;
            $newToLocation->count = $request->quantity * -1;
            $newToLocation->location = $request->from;
            $newToLocation->inventory_track_id = $Inventory->id;
            $newToLocation->item_id = (isset($request->item_id)) ? $request->item_id : null;
            $newToLocation->expiration_date = $request->expiration_date; 
            $newToLocation->from_id = $request->from_id;   
            $newToLocation->save();
        }

        if ($request->to == 'shipping' || $request->to == 'production' || $request->to == 'adjustment'){
            // If Shipping
            $newFromLocation = new InventoryLocation();
            $newFromLocation->barcode = $request->barcode;
            $newFromLocation->count = $request->quantity;
            $newFromLocation->location = $request->to;
            $newFromLocation->inventory_track_id = $Inventory->id;    
            $newFromLocation->expiration_date = $request->expiration_date;  
            $newFromLocation->from_id = $request->from_id;     
            $newFromLocation->item_id = (isset($request->item_id)) ? $request->item_id : null;   
            $newFromLocation->save();
        }
        if ($request->to != 'shipping' && $request->to != 'production' && $request->to != 'adjustment') {
            // When Moving From Location (Not Shipping)
            $ToLocation = new InventoryLocation();
            $ToLocation->barcode = $request->barcode;
            $ToLocation->count = $request->quantity ;
            $ToLocation->location = $request->to;
            $ToLocation->inventory_track_id = $Inventory->id;
            $ToLocation->expiration_date = $request->expiration_date; 
            $ToLocation->from_id = $request->from_id;               
            $ToLocation->item_id = (isset($request->item_id)) ? $request->item_id : null;
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
        $items =  Items::select('item.*')->join('item_identifiers', 'item.id', '=', 'item_identifiers.item_id')->where('item_identifiers.productIdentifier', '=', $barcode)->orWhere('item_identifiers.productIdentifier', '=', '0' . $barcode)->orWhere('item_identifiers.productIdentifier', '=',  substr($barcode, 1))->get();



        $action = 'no';


        $barcodes = InventoryModel::select('barcode')->where('barcode', '=', $barcode )->first();
        if (empty($barcodes)) {

            if (strlen($barcode) == 11) {
                $barcodes = InventoryModel::select('barcode')->where('barcode', '=', '0' . $barcode )->first();
                if (!empty($barcodes)) {
                    $action = 'append';
                    $barcode = '0' . $barcode;
                }
            }
            elseif (strlen($barcode) == 12) {
                if ($barcode[0] == '0') {
                    $barcodes = InventoryModel::select('barcode')->where('barcode', '=',  substr($barcode, 1) )->first();
                    if (!empty($barcodes)) {
                        $action = 'append';
                        $barcode = substr($barcode, 1);
                    }                    
                }
            }
    
    
            if (empty($barcodes)) {              
                return response()->json(["error" => 'Barcode not found', 'items' => $items, 'status' => '404', 'action' => $action, 'barcode' => $barcode]);
            }
        }



            
        $from_to_query = InventoryModel::select('from', 'to', 'barcode')->where('barcode', '=', $barcode)->whereRaw("LOWER(`to`) != 'shipping' and LOWER(`to`) != 'production' and LOWER(`to`) != 'adjustment'")->get();
        $locations = array();
        foreach ($from_to_query as $k => $from_to_rec){
            if(!in_array($from_to_rec->to,$locations) && ($from_to_rec->to != 'Receiving' && $from_to_rec->to != 'Shipping' && $from_to_rec->to != 'Production') )
            {
                $locations[$k] = $from_to_rec->to;
            }
            elseif(!in_array($from_to_rec->from,$locations) && ($from_to_rec->from != 'Receiving' && $from_to_rec->from != 'Shipping' && $from_to_rec->from != 'Production') )
            {
                $locations[$k] = $from_to_rec->from;
            }
            if (isset($locations[$k])) {
                    $get_location_sum  = DB::table('inventory_location')
                     ->select(DB::raw('SUM(`count`) as qty'))
                     ->where('location', '=',  $locations[$k])
                     ->where('barcode', '=', $barcode)
                     ->where('deleted_at', '=', null)
                    //  ->groupBy('expiration_date')
                     ->first();

                     if ($get_location_sum->qty < 1) {
                        unset($locations[$k]);
                     }
            }




            $locations = array_unique($locations);
        }
         
       return response()->json(["locations" => array_values($locations), "items" => $items, 'status' => 'success', 'action' => $action, 'barcode' => $barcode]);

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
            //  ->groupBy('expiration_date')
             ->get();

        if (!empty($getLocationDetails)) {
            foreach($getLocationDetails as $key => $locationDeatail){

                $data[$key]['count'] = $locationDeatail->quantity;
                $data[$key]['expiration'] = $locationDeatail->expiration_date;
                $data[$key]['from_id'] = $locationDeatail->from_id;
                if ($locationDeatail->quantity < 1) {
                    unset($data[$key]);
                }
            }
            
        }

       return response()->json(["data" =>array_values( $data), 'status' => 'success']);

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
