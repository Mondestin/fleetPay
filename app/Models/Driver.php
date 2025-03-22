<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'driver_uber_id',
        'last_name',
        'full_name',
        'phone_number',
        'email',
        'status',
        'user_id',
    ];

    /**
     * Get all platform earnings for the driver.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function platformEarnings()
    {
        return $this->hasMany(PlatformEarning::class);
    }

    /**
     * Get the user for the driver.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 