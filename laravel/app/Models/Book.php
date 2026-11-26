<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'author', 'publisher', 'language', 'book_condition', 'availability_status', 'published_year', 'volume_count',
        'isbn', 'page_count', 'dimensions', 'cover_image', 'summary',
        'additional_information', 'search_text', 'is_published', 'is_featured', 'featured_sort_order',
        'meta_title', 'meta_description', 'og_image',
    ];

    protected $appends = ['cover_url'];

    protected function casts(): array
    {
        return [
            'published_year' => 'integer',
            'volume_count' => 'integer',
            'page_count' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'featured_sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $book): void {
            if (blank($book->slug)) {
                $book->slug = Str::slug($book->title).'-'.Str::lower(Str::random(6));
            }

            $book->search_text = Str::ascii(Str::lower(Str::squish(implode(' ', array_filter([
                $book->title,
                $book->author,
                $book->publisher,
                $book->summary,
                $book->additional_information,
                $book->language,
                $book->book_condition,
                $book->availability_status,
            ])))));
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(BookImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image ? Storage::disk('public')->url($this->og_image) : $this->cover_url;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeBookOnly(Builder $query): Builder
    {
        return $query->whereDoesntHave('categories', function ($categoryQuery): void {
            $categoryQuery->whereIn('slug', ['gifts', 'qua-tang'])
                ->orWhere('slug', 'like', '%gift%')
                ->orWhere('name', 'like', '%gift%')
                ->orWhere('name', 'like', '%quà tặng%');
        });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $normalizedTerm = Str::ascii(Str::lower(Str::squish($term)));

        return $query->where(function (Builder $query) use ($term, $normalizedTerm): void {
            $query->where('title', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%")
                ->orWhere('publisher', 'like', "%{$term}%")
                ->orWhere('isbn', 'like', "%{$term}%")
                ->orWhere('summary', 'like', "%{$term}%")
                ->orWhere('search_text', 'like', "%{$normalizedTerm}%")
                ->orWhereHas('categories', fn ($q) => $q
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('search_name', 'like', "%{$normalizedTerm}%"));
        });
    }
}
