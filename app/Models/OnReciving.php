<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnReciving extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='on_reciving';
    protected $softDelete = true;
}
