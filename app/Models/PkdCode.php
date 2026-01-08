<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkdCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'version',
        'parent_code',
        'level',
        'is_leaf',
    ];

    protected $casts = [
        'is_leaf' => 'bool',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_code', 'code')
            ->where('version', $this->version);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_code', 'code')
            ->where('version', $this->version);
    }

    public function scopeVersion($query, string $version)
    {
        return $query->where('version', $version);
    }
}
