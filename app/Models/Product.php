<?php

namespace App\Models;
use App\Collections\ProductCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'description', 'category_id', 'stock', 'image', 'is_featured', 'rating'];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_featured' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function newCollection(array $models = []): ProductCollection
    {
        return new ProductCollection($models);
    }




public function getImageSizeAttribute()
{
    if ($this->image && Storage::disk('public')->exists($this->image)) {
        $size = Storage::disk('public')->size($this->image);

        return Number::fileSize($size);
    }

    return '0 B';
}
}
