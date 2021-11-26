<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itemlisting extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='item_listing';
    protected $softDelete = true;
}
