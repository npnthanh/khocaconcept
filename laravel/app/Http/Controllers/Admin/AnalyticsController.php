<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\SearchQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __invoke(Request $request)
    {
        $range = (int) $request->input('range', 30);
        if (! in_array($range, [7, 30, 90, 365], true)) {
            $range = 30;
        }
        $since = now()->subDays($range - 1)->startOfDay();
        $events = AnalyticsEvent::query()->where('created_at', '>=', $since);

        $topBooks = (clone $events)
            ->type('book_view')
            ->whereNotNull('book_id')
            ->select('book_id', DB::raw('COUNT(*) as views'))
            ->with('book')
            ->groupBy('book_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $topGifts = (clone $events)
            ->type('gift_view')
            ->whereNotNull('gift_id')
            ->select('gift_id', DB::raw('COUNT(*) as views'))
            ->with('gift')
            ->groupBy('gift_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $dailyViews = (clone $events)
            ->whereIn('event_type', ['book_view', 'gift_view'])
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as views'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('admin.analytics.index', [
            'range' => $range,
            'bookViews' => (clone $events)->whereIn('event_type', ['book_view', 'gift_view'])->count(),
            'zaloClicks' => (clone $events)->type('zalo_click')->count(),
            'emailClicks' => (clone $events)->type('email_click')->count(),
            'searches' => SearchQuery::sum('hits'),
            'noResultSearches' => SearchQuery::where('no_result_hits', '>', 0)->orderByDesc('no_result_hits')->limit(15)->get(),
            'topBooks' => $topBooks,
            'topGifts' => $topGifts,
            'dailyViews' => $dailyViews,
        ]);
    }
}
