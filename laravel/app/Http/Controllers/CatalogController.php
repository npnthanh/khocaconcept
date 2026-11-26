<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Gift;
use App\Models\HomeExploreTile;
use App\Models\AnalyticsEvent;
use App\Models\Banner;
use App\Models\Collection as BookCollection;
use App\Models\SearchQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class CatalogController extends Controller
{
    public function home()
    {
        $featuredBooks = Book::query()->published()->bookOnly()->with('categories')
            ->where('is_featured', true)
            ->orderBy('featured_sort_order')
            ->latest('id')
            ->take(12)
            ->get();

        $gifts = Gift::query()->published()->latest('id')->take(12)->get();

        // Keep the homepage available while an older deployment is waiting for the new table.
        try {
            $exploreTiles = HomeExploreTile::query()
                ->active()
                ->with('category')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->take(4)
                ->get();
        } catch (Throwable) {
            $exploreTiles = collect();
        }

        return view('catalog.home', [
            'categories' => Category::query()->where('is_active', true)
                ->whereNotIn('slug', ['gifts', 'qua-tang'])
                ->where('name', 'not like', '%gift%')
                ->where('name', 'not like', '%quà tặng%')
                ->orderBy('sort_order')->get(),
            'books' => Book::query()->published()->bookOnly()->with('categories')->latest()->take(16)->get(),
            'featuredBooks' => $featuredBooks,
            'gifts' => $gifts,
            'collections' => BookCollection::query()->published()->withCount('books')->orderBy('sort_order')->latest('id')->take(6)->get(),
            'banners' => Banner::query()->active()->get(),
            'exploreTiles' => $exploreTiles,
        ]);
    }

    public function index(Request $request)
    {
        $selectedCategory = $request->string('category')->trim()->toString();
        $term = $request->string('q')->trim()->toString();
        $sort = $request->string('sort')->toString();
        $year = $request->string('year')->trim()->toString();
        $author = $request->string('author')->trim()->toString();
        $publisher = $request->string('publisher')->trim()->toString();
        $language = $request->string('language')->trim()->toString();
        $condition = $request->string('condition')->trim()->toString();
        $availability = $request->string('availability')->trim()->toString();

        if (in_array($selectedCategory, ['gifts', 'qua-tang'], true)) {
            return redirect()->route('gifts.index', array_filter(['q' => $term, 'sort' => $sort]));
        }

        $categories = Category::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNotIn('slug', ['gifts', 'qua-tang'])
                    ->where('slug', 'not like', '%gift%')
                    ->where('name', 'not like', '%gift%')
                    ->where('name', 'not like', '%quà tặng%');
            })
            ->withCount(['books' => fn ($query) => $query->published()->bookOnly()])
            ->orderBy('sort_order')
            ->get();

        $selectedCatObj = $selectedCategory ? $categories->firstWhere('slug', $selectedCategory) : null;
        $books = Book::query()
            ->published()
            ->bookOnly()
            ->with('categories')
            ->search($term)
            ->when($selectedCategory, function ($query) use ($selectedCategory, $selectedCatObj) {
                if ($selectedCatObj) {
                    $query->whereHas('categories', fn ($cat) => $cat->where('categories.id', $selectedCatObj->id));
                } else {
                    $query->whereHas('categories', fn ($cat) => $cat->where('slug', $selectedCategory));
                }
            })
            ->when($year !== '' && ctype_digit($year), fn ($query) => $query->where('published_year', (int) $year))
            ->when($author, fn ($query) => $query->where('author', $author))
            ->when($publisher, fn ($query) => $query->where('publisher', $publisher))
            ->when($language, fn ($query) => $query->where('language', $language))
            ->when($condition, fn ($query) => $query->where('book_condition', $condition))
            ->when($availability, fn ($query) => $query->where('availability_status', $availability))
            ->when($sort === 'newest', fn ($query) => $query->latest('published_year')->latest('id'))
            ->when($sort === 'bestseller', fn ($query) => $query->orderByDesc('id'))
            ->when(!in_array($sort, ['newest', 'bestseller']), fn ($query) => $query->orderBy('title'))
            ->paginate(12)
            ->withQueryString();

        $filterBooks = Book::query()->published()->bookOnly();
        $years = (clone $filterBooks)->whereNotNull('published_year')->distinct()->orderByDesc('published_year')->pluck('published_year');
        $authors = (clone $filterBooks)->whereNotNull('author')->where('author', '<>', '')->distinct()->orderBy('author')->pluck('author');
        $publishers = (clone $filterBooks)->whereNotNull('publisher')->where('publisher', '<>', '')->distinct()->orderBy('publisher')->pluck('publisher');
        $languages = (clone $filterBooks)->whereNotNull('language')->where('language', '<>', '')->distinct()->orderBy('language')->pluck('language');

        $bookConditions = [
            'unknown' => 'Chưa cập nhật',
            'new' => 'Mới',
            'used' => 'Đã qua sử dụng',
            'limited' => 'Ấn bản giới hạn',
            'signed' => 'Có chữ ký',
        ];
        $availabilityStatuses = [
            'available' => 'Đang có',
            'reserved' => 'Tạm giữ',
            'sold' => 'Đã bán',
            'coming_soon' => 'Sắp về',
        ];

        $popularSearches = collect();
        if ($term !== '') {
            try {
                SearchQuery::record($term, $books->total());
            } catch (Throwable) {
                // Analytics must not make the catalogue unavailable during rollout.
            }
        }
        try {
            $popularSearches = SearchQuery::popular();
        } catch (Throwable) {
            // The table may not exist on an older deployment yet.
        }

        $nearbyBooks = collect();
        if ($term !== '' && $books->total() === 0) {
            $nearbyBooks = $this->findSimilarBooks($term);
        }

        return view('catalog.index', [
            'books' => $books,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'selectedCatObj' => $selectedCatObj,
            'term' => $term,
            'sort' => $sort,
            'year' => $year,
            'author' => $author,
            'publisher' => $publisher,
            'language' => $language,
            'condition' => $condition,
            'availability' => $availability,
            'years' => $years,
            'authors' => $authors,
            'publishers' => $publishers,
            'languages' => $languages,
            'bookConditions' => $bookConditions,
            'availabilityStatuses' => $availabilityStatuses,
            'popularSearches' => $popularSearches,
            'nearbyBooks' => $nearbyBooks,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $term = $request->string('q')->trim()->toString();
        $books = $term === ''
            ? collect()
            : Book::query()
                ->published()
                ->bookOnly()
                ->with('categories')
                ->search($term)
                ->orderBy('title')
                ->limit(8)
                ->get();

        $popular = collect();
        try {
            $popular = SearchQuery::popular(6)->map(fn (SearchQuery $query) => [
                'term' => $query->term,
                'hits' => $query->hits,
            ]);
        } catch (Throwable) {
            // Keep autocomplete working even before the analytics table is deployed.
        }

        return response()->json([
            'books' => $books->map(fn (Book $book) => [
                'title' => $book->title,
                'author' => $book->author,
                'category' => $book->categories->first()?->name,
                'url' => route('books.show', $book),
            ])->values(),
            'popular' => $popular->values(),
        ]);
    }

    public function show(Book $book)
    {
        abort_unless($book->is_published && $book->newQuery()->bookOnly()->whereKey($book->id)->exists(), 404);

        try {
            AnalyticsEvent::record('book_view', $book);
        } catch (Throwable) {
            // Analytics must never prevent the book page from rendering.
        }

        $relatedBooks = Book::query()->published()->bookOnly()->with('categories')
            ->whereKeyNot($book->id)
            ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $book->categories->modelKeys()))
            ->latest('id')
            ->take(6)->get();

        if ($relatedBooks->count() < 4) {
            $filler = Book::query()->published()->bookOnly()->with('categories')
                ->whereKeyNot($book->id)
                ->whereNotIn('id', $relatedBooks->pluck('id'))
                ->inRandomOrder()
                ->take(6 - $relatedBooks->count())
                ->get();
            $relatedBooks = $relatedBooks->concat($filler);
        }

        return view('catalog.show', [
            'book' => $book->load(['categories', 'galleryImages']),
            'relatedBooks' => $relatedBooks,
        ]);
    }

    public function giftsIndex(Request $request)
    {
        $term = $request->string('q')->trim()->toString();
        $sort = $request->string('sort')->toString();

        $gifts = Gift::query()
            ->published()
            ->search($term)
            ->when($sort === 'newest', fn ($query) => $query->latest('id'))
            ->when($sort === 'popular', fn ($query) => $query->latest('id'))
            ->when(! in_array($sort, ['newest', 'popular'], true), fn ($query) => $query->orderBy('title'))
            ->paginate(12)
            ->withQueryString();

        return view('catalog.gifts.index', compact('gifts', 'term', 'sort'));
    }

    public function giftShow(Gift $gift)
    {
        abort_unless($gift->is_published, 404);

        try {
            AnalyticsEvent::record('gift_view', $gift);
        } catch (Throwable) {
            // Analytics must never prevent the Gift page from rendering.
        }

        $relatedGifts = Gift::query()->published()
            ->whereKeyNot($gift->id)
            ->when($gift->product_type, fn ($query) => $query->where('product_type', $gift->product_type))
            ->latest('id')
            ->take(4)
            ->get();

        return view('catalog.gifts.show', [
            'gift' => $gift->load('galleryImages'),
            'relatedGifts' => $relatedGifts,
        ]);
    }

    public function collection(BookCollection $collection)
    {
        abort_unless($collection->is_published, 404);

        return view('catalog.collection', [
            'collection' => $collection->load(['books.categories', 'books.galleryImages']),
        ]);
    }

    public function contactClick(Book $book, string $channel)
    {
        abort_unless($book->is_published, 404);
        abort_unless(in_array($channel, ['facebook', 'zalo', 'email'], true), 404);

        $contactEmail = \App\Models\Setting::get('contact_email');
        $contactZalo = \App\Models\Setting::get('contact_zalo');
        $contactFacebook = \App\Models\Setting::get('contact_facebook') ?: \App\Models\Setting::get('contact_messenger');

        try {
            AnalyticsEvent::record($channel.'_click', $book);
        } catch (Throwable) {
            // Analytics is best-effort only.
        }

        if ($channel === 'facebook') {
            $fbHref = $contactFacebook
                ? (\Illuminate\Support\Str::startsWith($contactFacebook, ['http://', 'https://']) ? $contactFacebook : 'https://facebook.com/'.$contactFacebook)
                : null;

            return $fbHref ? redirect()->away($fbHref) : redirect()->route('books.show', $book);
        }

        if ($channel === 'zalo') {
            $zaloHref = $contactZalo
                ? (\Illuminate\Support\Str::startsWith($contactZalo, ['http://', 'https://'])
                    ? $contactZalo
                    : 'https://zalo.me/'.preg_replace('/\D+/', '', $contactZalo))
                : null;

            return $zaloHref ? redirect()->away($zaloHref) : redirect()->route('books.show', $book);
        }

        if (! $contactEmail) {
            return redirect()->route('books.show', $book);
        }

        $message = 'Xin chào, tôi quan tâm đến ấn bản "'.$book->title.'". Tôi muốn được tư vấn thêm.';
        $mailto = 'mailto:'.$contactEmail.'?subject='.rawurlencode('Quan tâm ấn bản: '.$book->title).'&body='.rawurlencode($message.'\n\n'.route('books.show', $book));

        return redirect()->away($mailto);
    }

    public function giftContactClick(Gift $gift, string $channel)
    {
        abort_unless($gift->is_published, 404);
        abort_unless(in_array($channel, ['facebook', 'zalo', 'email'], true), 404);

        $contactEmail = \App\Models\Setting::get('contact_email');
        $contactZalo = \App\Models\Setting::get('contact_zalo');
        $contactFacebook = \App\Models\Setting::get('contact_facebook') ?: \App\Models\Setting::get('contact_messenger');

        try {
            AnalyticsEvent::record($channel.'_click', $gift);
        } catch (Throwable) {
            // Analytics is best-effort only.
        }

        if ($channel === 'facebook') {
            $fbHref = $contactFacebook
                ? (Str::startsWith($contactFacebook, ['http://', 'https://']) ? $contactFacebook : 'https://facebook.com/'.$contactFacebook)
                : null;

            return $fbHref ? redirect()->away($fbHref) : redirect()->route('gifts.show', $gift);
        }

        if ($channel === 'zalo') {
            $zaloHref = $contactZalo
                ? (Str::startsWith($contactZalo, ['http://', 'https://'])
                    ? $contactZalo
                    : 'https://zalo.me/'.preg_replace('/\D+/', '', $contactZalo))
                : null;

            return $zaloHref ? redirect()->away($zaloHref) : redirect()->route('gifts.show', $gift);
        }

        if (! $contactEmail) {
            return redirect()->route('gifts.show', $gift);
        }

        $message = 'Xin chào, tôi quan tâm đến sản phẩm "'.$gift->title.'". Tôi muốn được tư vấn thêm.';
        $mailto = 'mailto:'.$contactEmail.'?subject='.rawurlencode('Quan tâm sản phẩm Gift: '.$gift->title).'&body='.rawurlencode($message.'\n\n'.route('gifts.show', $gift));

        return redirect()->away($mailto);
    }

    private function findSimilarBooks(string $term)
    {
        $normalizedTerm = SearchQuery::normalize($term);
        $tokens = collect(preg_split('/\s+/', $normalizedTerm) ?: [])
            ->filter(fn (string $token) => mb_strlen($token) >= 2)
            ->values();

        return Book::query()
            ->published()
            ->bookOnly()
            ->with('categories')
            ->get()
            ->map(function (Book $book) use ($normalizedTerm, $tokens): Book {
                $title = SearchQuery::normalize($book->title);
                $haystack = SearchQuery::normalize(implode(' ', array_filter([
                    $book->title,
                    $book->author,
                    $book->publisher,
                    $book->summary,
                ])));

                $percent = 0.0;
                similar_text($normalizedTerm, $title, $percent);
                $score = $percent;

                foreach ($tokens as $token) {
                    if (Str::contains($haystack, $token)) {
                        $score += 25;
                    }
                }

                $book->setAttribute('similarity_score', $score);

                return $book;
            })
            ->filter(fn (Book $book) => (float) $book->getAttribute('similarity_score') >= 25)
            ->sortByDesc('similarity_score')
            ->take(4)
            ->values();
    }

    public function about()
    {
        $defaults = \App\Http\Controllers\Admin\AboutPageController::getDefaults();
        $siteName = \App\Models\Setting::get('site_name', 'KHO - CA');
        $defaults['about_story_lead'] = str_replace(':site_name', $siteName, $defaults['about_story_lead']);

        $aboutData = [];
        foreach ($defaults as $key => $defaultVal) {
            $val = \App\Models\Setting::get($key, $defaultVal);
            if ($key === 'about_split_text' && is_string($val) && str_contains($val, 'Từ những cuốn truyện tranh kinh điển')) {
                $val = '';
            }
            $aboutData[$key] = $val;
        }

        $totalBooks = Book::query()->published()->bookOnly()->count();
        $totalCategories = Category::query()->where('is_active', true)
            ->whereNotIn('slug', ['gifts', 'qua-tang'])
            ->where('name', 'not like', '%gift%')
            ->where('name', 'not like', '%quà tặng%')
            ->count();
        $featuredBooks = Book::query()->published()->bookOnly()->with('categories')->latest()->take(4)->get();
        $categories = Category::query()->where('is_active', true)
            ->whereNotIn('slug', ['gifts', 'qua-tang'])
            ->where('name', 'not like', '%gift%')
            ->where('name', 'not like', '%quà tặng%')
            ->orderBy('sort_order')->take(6)->get();

        return view('catalog.about', [
            'aboutData' => $aboutData,
            'totalBooks' => $totalBooks,
            'totalCategories' => $totalCategories,
            'featuredBooks' => $featuredBooks,
            'categories' => $categories,
        ]);
    }

    public function copyright()
    {
        $defaults = \App\Http\Controllers\Admin\PolicyPageController::getDefaults('copyright');
        $pageData = [
            'kicker' => \App\Models\Setting::get('page_copyright_kicker', $defaults['kicker']),
            'title' => \App\Models\Setting::get('page_copyright_title', $defaults['title']),
            'meta' => \App\Models\Setting::get('page_copyright_meta', $defaults['meta']),
            'content' => \App\Models\Setting::get('page_copyright_content', $defaults['content']),
        ];

        return view('catalog.copyright', compact('pageData'));
    }

    public function terms()
    {
        $defaults = \App\Http\Controllers\Admin\PolicyPageController::getDefaults('terms');
        $pageData = [
            'kicker' => \App\Models\Setting::get('page_terms_kicker', $defaults['kicker']),
            'title' => \App\Models\Setting::get('page_terms_title', $defaults['title']),
            'meta' => \App\Models\Setting::get('page_terms_meta', $defaults['meta']),
            'content' => \App\Models\Setting::get('page_terms_content', $defaults['content']),
        ];

        return view('catalog.terms', compact('pageData'));
    }

    public function privacy()
    {
        $defaults = \App\Http\Controllers\Admin\PolicyPageController::getDefaults('privacy');
        $pageData = [
            'kicker' => \App\Models\Setting::get('page_privacy_kicker', $defaults['kicker']),
            'title' => \App\Models\Setting::get('page_privacy_title', $defaults['title']),
            'meta' => \App\Models\Setting::get('page_privacy_meta', $defaults['meta']),
            'content' => \App\Models\Setting::get('page_privacy_content', $defaults['content']),
        ];

        return view('catalog.privacy', compact('pageData'));
    }
}
