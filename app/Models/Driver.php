<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasUuids;

    protected $fillable = [
        'first_name',
        'driver_uber_id',
        'last_name',
        'full_name',
        'phone_number',
        'email',
        'status',
    ];

    public function platformEarnings()
    {
        return $this->hasMany(PlatformEarning::class);
    }

} 