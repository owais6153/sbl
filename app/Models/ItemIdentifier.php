<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemIdentifier extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='item_identifiers';
    protected $softDelete = true;

    public function inventoryLocationTracking(){
        return $this->hasMany('App\Models\InventoryLocationTracking','item_id','item_id');
    }
    public function item(){
        return $this->belongsTo('App\Models\Items','item_id'); 
        
    }
    public function  allitems(){
        return $this->item()->withTrashed();
    }
}
