<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'lp',
        'nip',
        'regon',
        'full_name',
        'slug',
        'nazwisko',
        'imie',
        'telefon',
        'email',
        'adres_www',
        'wojewodztwo',
        'powiat',
        'gmina',
        'miejscowosc',
        'ulica',
        'nr_budynku',
        'nr_lokalu',
        'kod_pocztowy',
        'glowny_kod_pkd',
        'pozostale_kody_pkd',
        'rok_pkd',
        'status_dzialalnosci',
        'data_rozpoczecia_dzialalnosci',
        'data_zakonczenia_dzialalnosci',
        'data_zawieszenia_dzialalnosci',
        'data_wznowienia_dzialalnosci',
        'imported_at',
    ];

    protected $casts = [
        'data_rozpoczecia_dzialalnosci' => 'date',
        'data_zakonczenia_dzialalnosci' => 'date',
        'data_zawieszenia_dzialalnosci' => 'date',
        'data_wznowienia_dzialalnosci' => 'date',
        'imported_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Business $business): void {
            $business->slug = $business->slug ?: static::generateSlug($business->full_name, $business->nip);
            $business->imported_at = $business->imported_at ?? now();
        });

        static::saving(function (Business $business): void {
            $business->slug = $business->slug ?: static::generateSlug($business->full_name, $business->nip);
        });
    }

    public function pkdCodes()
    {
        return $this->hasMany(BusinessPkdCode::class);
    }

    public function rawPayloads()
    {
        return $this->hasMany(BusinessRawPayload::class);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['q'] ?? null, function ($query, $term) {
                $term = trim($term);
                $query->where(function ($q) use ($term) {
                    $q->where('full_name', 'like', '%' . $term . '%')
                        ->orWhere('nip', 'like', '%' . $term . '%')
                        ->orWhere('regon', 'like', '%' . $term . '%')
                        ->orWhere('imie', 'like', '%' . $term . '%')
                        ->orWhere('nazwisko', 'like', '%' . $term . '%');
                });
            })
            ->when($filters['imie'] ?? null, fn($q, $v) => $q->where('imie', 'like', '%' . trim($v) . '%'))
            ->when($filters['nazwisko'] ?? null, fn($q, $v) => $q->where('nazwisko', 'like', '%' . trim($v) . '%'))
            ->when($filters['pkd'] ?? null, function ($q, $pkd) {
                if (is_array($pkd)) {
                    $clean = array_filter(array_map('trim', $pkd));
                    if (! empty($clean)) {
                        $q->whereIn('glowny_kod_pkd', $clean);
                    }
                } else {
                    $q->where('glowny_kod_pkd', 'like', $pkd . '%');
                }
            })
            ->when($filters['wojewodztwo'] ?? null, fn($q, $v) => $q->where('wojewodztwo', $v))
            ->when($filters['powiat'] ?? null, fn($q, $v) => $q->where('powiat', $v))
            ->when($filters['gmina'] ?? null, fn($q, $v) => $q->where('gmina', $v))
            ->when($filters['miejscowosc'] ?? null, fn($q, $v) => $q->where('miejscowosc', $v))
            ->when($filters['kod_pocztowy'] ?? null, fn($q, $v) => $q->where('kod_pocztowy', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status_dzialalnosci', $v))
            ->when($filters['date_from'] ?? null, fn($q, $v) => $q->whereDate('data_rozpoczecia_dzialalnosci', '>=', $v))
            ->when($filters['date_to'] ?? null, fn($q, $v) => $q->whereDate('data_rozpoczecia_dzialalnosci', '<=', $v));
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('imported_at')->orderByDesc('id');
    }

    public static function generateSlug(?string $name, ?string $nip): string
    {
        $base = Str::slug($name ?: ($nip ?: uniqid()));

        return $nip ? $base . '-' . strtolower($nip) : $base;
    }
}
