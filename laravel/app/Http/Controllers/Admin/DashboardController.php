<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Gift;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'bookCount' => Book::bookOnly()->count(),
            'publishedBookCount' => Book::bookOnly()->where('is_published', true)->count(),
            'giftCount' => Gift::count(),
            'categoryCount' => Category::whereNotIn('slug', ['gifts', 'qua-tang'])->where('name', 'not like', '%gift%')->where('name', 'not like', '%quà tặng%')->count(),
            'latestBooks' => Book::bookOnly()->with('categories')->latest()->take(6)->get(),
        ]);
    }
}
