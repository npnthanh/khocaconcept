<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['event_type', 'book_id', 'gift_id', 'metadata', 'ip_address', 'user_agent', 'created_at'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'created_at' => 'datetime'];
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }

    public static function record(string $eventType, ?Model $item = null, array $metadata = []): void
    {
        static::create([
            'event_type' => $eventType,
            'book_id' => $item instanceof Book ? $item->id : null,
            'gift_id' => $item instanceof Gift ? $item->id : null,
            'metadata' => $metadata ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('event_type', $type);
    }
}
