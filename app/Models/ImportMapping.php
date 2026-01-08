<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ImportMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'format',
        'mapping',
        'static_values',
        'detected_columns',
        'last_used_at',
    ];

    protected $casts = [
        'mapping' => 'array',
        'static_values' => 'array',
        'detected_columns' => 'array',
        'last_used_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ImportMapping $mapping): void {
            $mapping->slug = $mapping->slug ?: Str::slug($mapping->name);
        });

        static::saving(function (ImportMapping $mapping): void {
            $mapping->slug = $mapping->slug ?: Str::slug($mapping->name);
        });
    }
}
