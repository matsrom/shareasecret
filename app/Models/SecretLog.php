<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecretLog extends Model
{
    protected $fillable = [
        'secret_id',
        'ip_address',
        'browser',
        'os',
        'device',
        'country',
        'city',
        'access_date',
        'is_successful'
    ];

    public function secret()
    {
        return $this->belongsTo(Secret::class);
    }
}
