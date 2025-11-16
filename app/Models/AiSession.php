<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSession extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','intent','user_payload','ai_response','cost'];

    protected $casts = [
        'user_payload' => 'array',
        'ai_response' => 'array',
        'cost' => 'decimal:4'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
