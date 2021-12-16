<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use DataTables;
use App\Models\ItemIdentifier;
use App\Models\InventoryLocationTracking as InventoryModel;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryLocation;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ImportToNoLocation;
use App\Models\User;
use League\Fractal\Resource\Item;

class ItemsController extends Controller
{
    public function index(){
        return view('itemslist');
    }

    public function getItems(){
        $model = Items::query();

        return DataTables::eloquent($model)
        ->filter(function ($query) {
            $s = request('search');
            if (isset($s['value']) && !empty($s['value'])) {
                $query->select('item.*', 'item_identifiers.productIdentifier')->join('item_identifiers', 'item.id', '=', 'item_identifiers.item_id');

                return $query;
            }

        }, true)
        ->addColumn('productIdentifier', function($row){
            $ItemIdentifiers = ItemIdentifier::select('productIdentifier')->where('item_id', '=', $row->id)->get();
            if (!empty($ItemIdentifiers)) {
                $html = '';
                foreach ($ItemIdentifiers as $key => $ItemIdentifier){
                    $html .= $ItemIdentifier->productIdentifier;
                    if (isset($ItemIdentifiers[$key + 1])) {
                        $html .= ',';
                    }
                    $html .= ' ';
                }
                return $html;
            }
        })
        ->toJson();
    }
    function onHoldToNoLocation(Request $request){
        
        ini_set('max_execution_time', '300');
        $offset =0;
        $limit =1000;
        $count=Items::all()->count();
        $items = Items::where('ridgefield_onhand',">",'0')->where('ridgefield_onhand',"!=",'null')->skip($offset)->take($limit)->get();
        
        $count = round($count /1000);
        
        while(true){
            foreach($items as $item){
                $LocationDetails =  DB::table('inventory_location')
                ->select(DB::raw('SUM(`count`) as `quantity`,barcode'))
                ->where('item_id', '=', $item->id)->where('barcode', '!=','null')
                ->where('deleted_at', '=', null)->whereRaw("LOWER(`location`) != 'shipping' and LOWER(`location`) != 'production' and LOWER(`location`) != 'adjustment' and LOWER(`location`) != 'receiving'")
                ->first();
                if(isset($LocationDetails->barcode)){
                
                    $checkcode = DB::table('inventory_location')
                    ->select(DB::raw('barcode'))
                    ->where('barcode', '=', $LocationDetails->barcode)->orWhere('barcode', '=', '0' . $LocationDetails->barcode)->orWhere('barcode', '=',  substr($LocationDetails->barcode, 1))
                    ->where('deleted_at', '=', null)
                    ->first();
                    $barcode = $checkcode->barcode;
                    $item_id =$item->id;
                    if ($LocationDetails->quantity < $item->ridgefield_onhand) {
                                        
                        $Inventory_track = new InventoryModel();
                        $Inventory_track->user_id = 1;
                        $Inventory_track->barcode = $barcode;
                        $Inventory_track->quantity = $item->ridgefield_onhand - $LocationDetails->quantity;
                        $Inventory_track->from = 'Adjustment';
                        $Inventory_track->item_id = $item_id;
                        $Inventory_track->to = 'NoLocation';
                        $Inventory_track->save();
                        $ToLocation = new InventoryLocation();
                        $ToLocation->barcode = $barcode;
                        $ToLocation->count =  $item->ridgefield_onhand - $LocationDetails->quantity;
                        $ToLocation->location = 'NoLocation';
                        $ToLocation->item_id = $item_id;
                        $ToLocation->inventory_track_id = $Inventory_track->id;
                        $ToLocation->save();
                    }
                }
            }
            $offset += $limit;
            
            
            $items = Items::where('ridgefield_onhand',">",'0')->where('ridgefield_onhand',"!=",'null')->skip($offset)->take($limit)->get();
            if($count < round($offset/1000)){
                
                break;
            }
        }
        
        if (isset($request->output) && $request->output = 'html') {
            
            return response()->json([ 'status' => 'success']);
        }
        return redirect()->back()->with('success', "Successfully Imported To No Location");
    }
    function RemoveFromNoLocation(){
        InventoryModel::where(['to'=>'NoLocation','from'=>'Adjustment'])->forceDelete();
        InventoryLocation::where('location','NoLocation')->forceDelete();
        return redirect()->back()->with('success', "Successfully Removed.");
    }
    public function getAllMoves(){
        $users = User::all();
        return view('getAllMoves', compact('users'));
    }
    public function getAllMovesData()
    {

        $model = InventoryModel::query()->join('users', 'user_id', '=', 'users.id')->where('to', '!=', 'NoLocation');

        return DataTables::eloquent($model)
        ->filter(function ($query) {
            $user = request('user');
            $start_date = request('start_date');
            $end_date = request('end_date');
            if (!empty($user)) {
                $query->where('user_id', '=', $user);                
            }
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59 ']);
            }
            return $query;

        }, true)
        ->addColumn('item_number', function($row){
            if ($row->item_id) {                
                $item = Items::select('item_number')->where('id', '=', $row->item_id)->first();
                return (isset($item['item_number'])) ? $item['item_number'] : 'Not Found';
            }
            else{
                return 'Not Found';
            }

        })
        ->addColumn('time', function($row){
            $created_at = $row->created_at;
            $created_at = date('m/d/Y g:i:s A', strtotime($created_at));      
            $datetime = new \DateTime($created_at);
            $la_time = new \DateTimeZone('America/New_York');
            $datetime->setTimezone($la_time);
            return  $datetime->format('m/d/Y g:i:s A'); 
        })
        ->toJson();

    }
    public function itemtoexport(){
       
        return view('itemslistexport');

    }
    public function getitemtoexport(){
        $model = Items::query()->leftJoin('item_identifiers', 'item_identifiers.item_id', '=', 'item.id');
        
        return DataTables::eloquent($model) 

        ->addColumn('totalqty', function($row){
            if($row->inventoryLocationTracking ){
                return $row->inventoryLocationTracking->sum('quantity');
            }
            return 0;
        })
        
        ->toJson();
    }
    public function exportCsvitem()
    {
       $fileName = 'items.csv';
       $data = Items::all();
       
    
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );
    
            $columns = array('Item Name', 'Barcode', 'Total', 'Onhand');
    
            $callback = function() use($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
    
                foreach ($data as $item) {
                    $row['Item Name']  = $item->item_number;
                    if($item->itemidentifier && isset($item->itemidentifier->productIdentifier)){
                        $row['Barcode'] = $item->itemidentifier->productIdentifier;
                    }else{
                        $row['Barcode'] = 'NOt Found';
                    }
                    if($item->inventoryLocationTracking ){
                        $row['Total']    = $item->inventoryLocationTracking->sum('quantity');
                    }
                    else{
                        $row['Total']    =0;
                    }
                    
                    $row['Onhand']  = $item->ridgefield_onhand;
                    
                    
                    fputcsv($file, array($row['Item Name'], $row['Barcode'], $row['Total'], $row['Onhand']));
                }
    
                fclose($file);
            };
    
            return response()->stream($callback, 200, $headers);
        }
    
    
}
