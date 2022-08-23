<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UsesUuid;

class Order extends Model
{
    use UsesUuid;
    use HasFactory;

    protected $guarded = [];

    // protected $casts = [
    //     'cart' => 'json',
    // ];
}
