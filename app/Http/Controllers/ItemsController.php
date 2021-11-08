<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use DataTables;

class ItemsController extends Controller
{
    public function index(){
        return view('itemslist');
    }

    public function getItems(){
        $model = Items::query();

        return DataTables::eloquent($model)
        ->toJson();
    }
}
