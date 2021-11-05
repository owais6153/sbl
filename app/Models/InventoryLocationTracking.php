<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryLocationTracking extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='inventory_location_tracking';
    protected $softDelete = true;
}
