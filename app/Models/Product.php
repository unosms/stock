<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'source_id',
        'image_path',
        'unit',
        'cost_price',
        'selling_price',
        'quantity',
        'min_quantity',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return route('items.image', ['path' => $this->image_path]);
    }
}
