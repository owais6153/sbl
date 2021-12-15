<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Items extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='item';
    protected $softDelete = true;
    public function itemidentifier(){
        return $this->hasOne('App\Models\ItemIdentifier','item_id');
    }
    public function inventoryLocationTracking(){
        return $this->hasMany('App\Models\InventoryLocationTracking','item_id');
    }
}
