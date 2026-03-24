<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price','description','category_id','stok','image'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
