<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PlatformEarning extends Model
{
    use HasUuids;

    protected $fillable = [
        'driver_id',
        'platform',
        'week_start_date',
        'earnings',
        'created_by',
        'commission_amount',
        'validated',
        'status'
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'validated' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 