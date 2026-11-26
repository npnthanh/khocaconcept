<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BookImage extends Model
{
    use HasFactory;

    protected $fillable = ['book_id', 'path', 'alt_text', 'sort_order'];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
