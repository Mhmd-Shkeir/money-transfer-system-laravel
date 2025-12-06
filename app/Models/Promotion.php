<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'title','description','discount_type','discount_value','valid_from','valid_to','is_active'
    ];
}
