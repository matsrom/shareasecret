<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secret extends Model
{
    /** @use HasFactory<\Database\Factories\SecretFactory> */
    use HasFactory;

    protected $fillable = [
        'secret_type', 
        'message', 
        'original_filename',
        'message_key', 
        'message_iv',	
        'message_tag',
        'url_identifier',
        'is_password_protected', 
        'password_hash', 
        'clicks_expiration', 
        'clicks_remaining', 
        'days_expiration', 
        'days_remaining', 
        'allow_manual_deletion', 
        'user_id', 
        'keep_track',
        'alias',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
