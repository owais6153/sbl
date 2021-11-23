<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkippedItemIdentifiers extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='skipped_itemidentifiers';
    protected $softDelete = true;
}
