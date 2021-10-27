<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLocationTracking extends Model
{
    use HasFactory;
    
    protected $table='inventory_location_tracking';
    protected $softDelete = true;
}
