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
            if ($row->status == 'completed' && Bouncer::can('replen_batches_details')) {
                $html = '<a href="'.route('replenDetail', ['id' => $row->id]).'" class="mr-3">View Detail</a>';
                if (Bouncer::can('replen_batches_export')) {
                    $html .='<a href="">Export</a>';
                }
                return $html;
            }
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
}
