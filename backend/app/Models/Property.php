<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    protected $fillable = [
        'title', 'description', 'price', 'location_id', 'type_id',
        'bedrooms', 'bathrooms', 'floor_area', 'status', 'admin_id',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'type_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function scopeFilter(Builder $query, array $f): Builder
    {
        return $query
            ->when($f['location_id'] ?? null, fn ($q, $v) => $q->where('location_id', $v))
            ->when($f['type_id'] ?? null, fn ($q, $v) => $q->where('type_id', $v))
            ->when($f['min_price'] ?? null, fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($f['max_price'] ?? null, fn ($q, $v) => $q->where('price', '<=', $v))
            ->when($f['bedrooms'] ?? null, fn ($q, $v) => $q->where('bedrooms', '>=', $v))
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where(
                fn ($w) => $w->where('title', 'like', "%{$v}%")->orWhere('description', 'like', "%{$v}%")
            ));
    }
}
