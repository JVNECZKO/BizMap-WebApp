<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPkdCode extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'pkd_code',
        'pkd_version',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function pkd()
    {
        return $this->belongsTo(PkdCode::class, 'pkd_code', 'code')
            ->where('version', $this->pkd_version ?? '2007');
    }
}
