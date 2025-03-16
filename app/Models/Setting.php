<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'value'
    ];

    /**
     * Get the user associated with the setting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the value of the setting.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed    
     */
    public static function getValue(string $name, $default = null)
    {
        $setting = static::where('name', $name)->first();
        return $setting ? $setting->value : $default;
    }
} 