<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    public static function get(string $key, $default = null)
    {
        return Cache::remember(static::cacheKey($key), now()->addMinutes(30), function () use ($key, $default) {
            $setting = static::query()->where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return $setting->castedValue();
        });
    }

    public static function setValue(string $key, $value, string $type = 'string'): Setting
    {
        $storedValue = in_array($type, ['json', 'array'], true) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(['key' => $key], [
            'value' => $storedValue,
            'type' => $type,
        ]);

        Cache::forget(static::cacheKey($key));

        return $setting;
    }

    public function castedValue()
    {
        return match ($this->type) {
            'int' => (int) $this->value,
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOL),
            'json', 'array' => json_decode($this->value ?? 'null', true),
            default => $this->value,
        };
    }

    protected static function cacheKey(string $key): string
    {
        return "setting_{$key}";
    }
}
