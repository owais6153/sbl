<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnHand extends Model
{
    use HasFactory;
    protected $table='on_hand';
    protected $softDelete = true;
}
