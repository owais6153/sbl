<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReplenBatch;
use App\Models\ReplenDetail;
use DataTables;
use Bouncer;


class ReplenController extends Controller
{
    public function index(){
        return view('replen_batch');
    }
    public function getReplenBatch()
    {
        $model = ReplenBatch::query();
        return DataTables::eloquent($model)
        ->addColumn('time', function($row){
            $created_at = $row->created_at;
            $created_at = date('m/d/Y h:i:s A', strtotime($created_at));      
            $datetime = new \DateTime($created_at);
            $la_time = new \DateTimeZone('America/New_York');
            $datetime->setTimezone($la_time);
            return  $datetime->format('m/d/Y g:i:s A'); 
        })
        ->addColumn('actions', function($row){
            $html="";
            if ($row->status == 'completed' && Bouncer::can('replen_batches_details')) {
                $html = '<a href="'.route('replenDetail', ['id' => $row->id]).'" class="mr-3">View Detail</a>';
               
               
            }
            if (Bouncer::can('replen_batches_export')) {
                $html .='<a href="'.route('getReplenDetailexport',$row->id).'">Export</a>';
            }
            return $html;
        })
        ->rawColumns(['actions'])
        ->toJson();
    }
    public function replenDetail(){        
        return view('replen_details');
    }
    public function getReplenDetail(){
        $model = ReplenDetail::query()->where('replen_batch_id', '=', request('id'));
        return DataTables::eloquent($model)
        ->toJson();
    }
    public function exportCsv($id)
{
   $fileName = 'replendetails_B'.$id.'.csv';
   $replen_details = ReplenDetail::query()->where('replen_batch_id', '=', $id)->get();
   

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Item Name', 'Urlid', 'Store SkU', 'Store', '30 Days Sale','Amazon inventory','Unsellable','OnHand','Amount to Replen');

        $callback = function() use($replen_details, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($replen_details as $item) {
                $row['Item Name']  = $item->item_name;
                $row['Urlid']    = $item->urlid;
                $row['Store SkU']    = $item->store_sku;
                $row['Store']  = $item->store;
                $row['30 Days Sale']  = $item->days_30_sales;
                $row['Amazon inventory']  = $item->amazon_inventory;
                $row['Unsellable']  = $item->unsellable;
                $row['OnHand']  = $item->on_hand_ridgefield;
                $row['Amount to Replen']  = $item->amount_to_replen;
                
                fputcsv($file, array($row['Item Name'], $row['Urlid'], $row['Store SkU'], $row['Store'], $row['30 Days Sale'], $row['Amazon inventory'], $row['Unsellable'], $row['OnHand'], $row['Amount to Replen']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
