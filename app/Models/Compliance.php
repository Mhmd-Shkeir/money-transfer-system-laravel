<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compliance extends Model
{
    protected $fillable = [
        'type',
        'description',
        'status',
        'created_by'
    ];

    public $timestamps = true;

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
