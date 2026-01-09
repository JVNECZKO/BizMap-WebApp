<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkdTaxonomy extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'subgroup_name',
        'group_slug',
        'subgroup_slug',
        'primary_code',
        'secondary_codes',
    ];

    protected $casts = [
        'secondary_codes' => 'array',
    ];
}
