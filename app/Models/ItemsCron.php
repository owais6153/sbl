<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsCron extends Model
{
    use HasFactory;
    protected $table='cron_item_import';
}
