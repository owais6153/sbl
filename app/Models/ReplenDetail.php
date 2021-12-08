<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReplenDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='replen_details';
    protected $softDelete = true;
    public function item(){
        return $this->belongsTo('App\Models\Items','item_id'); 
        
    }
}
