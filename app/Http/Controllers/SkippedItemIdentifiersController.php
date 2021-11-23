<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SkippedItemIdentifiers;
use DataTables;
use App\Models\Items;

class SkippedItemIdentifiersController extends Controller
{
    public function index(){
        return view('skipped_itemlist');
    }
    /* Query for getting skipped duplicate items

    SELECT item.item_number as 'item', ditem.item_number as 'duplicate item', skipped_itemidentifiers.barcode, skipped_itemidentifiers.created_at FROM `skipped_itemidentifiers` JOIN `item` as item ON `skipped_itemidentifiers`.`item_id` = `item`.`id` JOIN `item` as ditem ON ditem.id = skipped_itemidentifiers.duplicate_item_id where `skipped_itemidentifiers`.`item_id` != `skipped_itemidentifiers`.`duplicate_item_id` */

    public function getSkippedItems()
    {
        $model = SkippedItemIdentifiers::query()->whereRaw('skipped_itemidentifiers.item_id != skipped_itemidentifiers.duplicate_item_id');

        return DataTables::eloquent($model)
        ->addColumn('item', function($row){
            $Items = Items::select('item_number')->where('id', '=', $row->item_id)->first();
            if (!empty($Items)) {
                $html = $Items->item_number;
                return $html;
            }
        })
        ->addColumn('duplicate_item', function($row){
            $duplicate_item = Items::select('item_number')->where('id', '=', $row->duplicate_item_id)->first();
            if (!empty($duplicate_item)) {
                $html = $duplicate_item->item_number;
                return $html;
            }
        })
        ->toJson();
    }
}
