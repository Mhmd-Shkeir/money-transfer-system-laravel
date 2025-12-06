<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','subject','message','status','source'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

  
    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'support_ticket_id')->orderBy('created_at', 'asc');
    }
}
