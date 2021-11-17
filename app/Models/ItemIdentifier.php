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
}
