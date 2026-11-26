<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            try {
                $categories = \App\Models\Category::query()->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $e) {
                $categories = collect();
            }

            if ($categories->isEmpty()) {
                $defaultNames = [
                    'Văn học Việt Nam', 'Văn học nước ngoài', 'Thiếu nhi', 'Kỹ năng sống',
                    'Lịch sử', 'Khoa học', 'Kinh tế', 'Nghệ thuật',
                ];
                $categories = collect($defaultNames)->map(fn ($name) => (object)[
                    'name' => $name,
                    'slug' => \Illuminate\Support\Str::slug($name),
                ]);
            }

            $view->with('navCategories', $categories);
        });
    }
}
