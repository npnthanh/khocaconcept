<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HomeExploreTile extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'subtitle', 'image_path', 'link_type', 'category_id',
        'link_url', 'sort_order', 'overlay_opacity', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'overlay_opacity' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        // Static assets are already public; uploaded files live on the public disk.
        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function getResolvedUrlAttribute(): string
    {
        if ($this->link_type === 'category' && $this->category) {
            return route('books.index', ['category' => $this->category->slug]);
        }

        return $this->link_url ?: route('books.index');
    }
}
