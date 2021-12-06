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

use App\Models\InventoryLocationTracking;

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
        InventoryLocationTracking::where(['to'=>'NoLocation','from'=>'Adjustment'])->forceDelete();
        InventoryLocation::where('location','NoLocation')->forceDelete();
        return redirect()->back()->with('success', "Successfully Removed.");
    }
}
