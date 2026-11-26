<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GiftImage extends Model
{
    use HasFactory;

    protected $fillable = ['gift_id', 'path', 'alt_text', 'sort_order'];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
