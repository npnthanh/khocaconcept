<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'brand', 'product_type', 'sku', 'availability_status',
        'dimensions', 'cover_image', 'summary', 'additional_information',
        'search_text', 'is_published', 'meta_title', 'meta_description', 'og_image',
    ];

    protected $appends = ['cover_url'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $gift): void {
            if (blank($gift->slug)) {
                $gift->slug = Str::slug($gift->title).'-'.Str::lower(Str::random(6));
            }

            $gift->search_text = Str::ascii(Str::lower(Str::squish(implode(' ', array_filter([
                $gift->title,
                $gift->brand,
                $gift->product_type,
                $gift->sku,
                $gift->summary,
                $gift->additional_information,
                $gift->availability_status,
            ])))));
        });
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(GiftImage::class)->orderBy('sort_order')->orderBy('id');
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

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $normalizedTerm = Str::ascii(Str::lower(Str::squish($term)));

        return $query->where(function (Builder $query) use ($term, $normalizedTerm): void {
            $query->where('title', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('product_type', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('summary', 'like', "%{$term}%")
                ->orWhere('search_text', 'like', "%{$normalizedTerm}%");
        });
    }
}
