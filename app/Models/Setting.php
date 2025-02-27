<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getValue(string $name, $default = null)
    {
        $setting = static::where('name', $name)->first();
        return $setting ? $setting->value : $default;
    }
} 