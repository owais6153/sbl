<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnHand extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='on_hand';
    protected $softDelete = true;
}
