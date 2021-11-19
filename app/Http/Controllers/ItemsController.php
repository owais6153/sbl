<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use DataTables;
use App\Models\ItemIdentifier;


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
}
