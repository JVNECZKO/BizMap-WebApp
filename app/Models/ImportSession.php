<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ImportSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'filename',
        'path',
        'format',
        'mapping_id',
        'total_rows',
        'imported_rows',
        'chunk_size',
        'status',
        'detected_columns',
        'static_values',
        'message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'detected_columns' => 'array',
        'static_values' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ImportSession $session): void {
            $session->token = $session->token ?: Str::uuid()->toString();
        });
    }

    public function mapping()
    {
        return $this->belongsTo(ImportMapping::class, 'mapping_id');
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }
}
