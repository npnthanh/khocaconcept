<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::find($key);
            return ($setting && !is_null($setting->value) && $setting->value !== '') ? $setting->value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function set(string $key, ?string $value): void
    {
        try {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        } catch (\Throwable $e) {
            // Fallback gracefully if database issue
        }
    }
}
