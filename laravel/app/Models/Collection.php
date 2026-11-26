<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'subtitle', 'description', 'cover_image',
        'is_featured', 'is_published', 'sort_order',
        'meta_title', 'meta_description', 'og_image',
    ];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'is_published' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $collection): void {
            if (blank($collection->slug)) {
                $collection->slug = Str::slug($collection->title).'-'.Str::lower(Str::random(6));
            }
        });
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'collection_book')
            ->where('books.is_published', true)
            ->bookOnly()
            ->withPivot('sort_order')
            ->orderBy('collection_book.sort_order')
            ->orderBy('books.title');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image ? Storage::disk('public')->url($this->og_image) : $this->cover_url;
    }
}
