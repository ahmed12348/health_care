<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the promotions for the category.
     */
    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    /**
     * Get all of the category's media.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * Get the category's image URL.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        $firstMedia = $this->media()->where('file_type', 'image')->first();
        
        if ($firstMedia && $firstMedia->file_path) {
            return asset('storage/' . $firstMedia->file_path);
        }
        
        return asset('front/assets/img/categories/cat-1.jpg');
    }
}

