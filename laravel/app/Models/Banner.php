<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'eyebrow', 'description', 'text_position', 'image_path', 'mobile_image_path', 'link_url', 'link_label',
        'placement', 'starts_at', 'ends_at', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function getTextPositionAttribute(): string
    {
        return in_array($this->attributes['text_position'] ?? null, ['left', 'right', 'center']) ? $this->attributes['text_position'] : 'left';
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_active', true)
            ->where('placement', 'home')
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderBy('sort_order')->latest('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function getMobileImageUrlAttribute(): ?string
    {
        return $this->mobile_image_path ? Storage::disk('public')->url($this->mobile_image_path) : null;
    }
}
