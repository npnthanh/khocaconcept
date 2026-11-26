<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SearchQuery extends Model
{
    use HasFactory;

    protected $fillable = ['term', 'normalized_term', 'hits', 'result_count', 'no_result_hits', 'last_searched_at'];

    protected function casts(): array
    {
        return [
            'last_searched_at' => 'datetime',
            'hits' => 'integer',
            'result_count' => 'integer',
            'no_result_hits' => 'integer',
        ];
    }

    public static function record(string $term, ?int $resultCount = null): void
    {
        $term = mb_substr(Str::squish($term), 0, 160);
        $normalizedTerm = mb_substr(static::normalize($term), 0, 160);

        if (mb_strlen($normalizedTerm) < 2) {
            return;
        }

        $query = static::firstOrCreate(
            ['normalized_term' => $normalizedTerm],
            ['term' => $term, 'hits' => 0]
        );

        $attributes = [
            'term' => $term,
            'hits' => (int) $query->hits + 1,
            'last_searched_at' => now(),
        ];

        if ($resultCount !== null) {
            $attributes['result_count'] = $resultCount;
            $attributes['no_result_hits'] = (int) $query->no_result_hits + ($resultCount === 0 ? 1 : 0);
        }

        $query->forceFill($attributes)->save();
    }

    public static function popular(int $limit = 8)
    {
        return static::query()
            ->where('hits', '>', 0)
            ->orderByDesc('hits')
            ->orderByDesc('last_searched_at')
            ->limit($limit)
            ->get(['term', 'hits', 'no_result_hits']);
    }

    public static function normalize(string $term): string
    {
        return Str::ascii(Str::lower(Str::squish($term)));
    }
}
