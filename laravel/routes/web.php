<?php

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\BookImportController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\GiftController as AdminGiftController;
use App\Http\Controllers\Admin\HomeExploreController as AdminHomeExploreController;
use App\Http\Controllers\Admin\AboutPageController;
use App\Http\Controllers\Admin\PolicyPageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/sach', [CatalogController::class, 'index'])->name('books.index');
Route::get('/sach/goi-y', [CatalogController::class, 'suggestions'])->name('books.suggestions');
Route::get('/qua-tang', [CatalogController::class, 'giftsIndex'])->name('gifts.index');
Route::get('/qua-tang/{gift:slug}/lien-he/{channel}', [CatalogController::class, 'giftContactClick'])->name('gifts.contact.click');
Route::get('/qua-tang/{gift:slug}', [CatalogController::class, 'giftShow'])->name('gifts.show');
Route::get('/bo-suu-tap/{collection:slug}', [CatalogController::class, 'collection'])->name('collections.show');
Route::get('/sach/{book:slug}/lien-he/{channel}', [CatalogController::class, 'contactClick'])->name('books.contact.click');
Route::get('/sach/{book:slug}', [CatalogController::class, 'show'])->name('books.show');
Route::get('/gioi-thieu', [CatalogController::class, 'about'])->name('about');
Route::get('/chinh-sach-ban-quyen', [CatalogController::class, 'copyright'])->name('copyright');
Route::get('/dieu-khoan-su-dung', [CatalogController::class, 'terms'])->name('terms');
Route::get('/chinh-sach-bao-mat', [CatalogController::class, 'privacy'])->name('privacy');

Route::middleware('guest')->group(function (): void {
    Route::get('/admin/dang-nhap', [AuthController::class, 'create'])->name('login');
    Route::post('/admin/dang-nhap', [AuthController::class, 'store'])->name('login.store');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/thong-ke', AdminAnalyticsController::class)->name('analytics.index');
    Route::post('/dang-xuat', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/sach/import', [BookImportController::class, 'create'])->name('books.import.create');
    Route::post('/sach/import', [BookImportController::class, 'store'])->name('books.import.store');
    Route::post('/sach/import/xem-truoc', [BookImportController::class, 'preview'])->name('books.import.preview');
    Route::post('/sach/import/xac-nhan', [BookImportController::class, 'confirm'])->name('books.import.confirm');
    Route::post('/sach/tac-vu-hang-loat', [AdminBookController::class, 'bulk'])->name('books.bulk');
    Route::delete('/sach/{book}/gallery/{image}', [AdminBookController::class, 'destroyImage'])->name('books.gallery.destroy');
    Route::resource('sach', AdminBookController::class)->parameters(['sach' => 'book'])->names('books')->except('show');
    Route::post('/qua-tang/tac-vu-hang-loat', [AdminGiftController::class, 'bulk'])->name('gifts.bulk');
    Route::delete('/qua-tang/{gift}/gallery/{image}', [AdminGiftController::class, 'destroyImage'])->name('gifts.gallery.destroy');
    Route::resource('qua-tang', AdminGiftController::class)->parameters(['qua-tang' => 'gift'])->names('gifts')->except('show');
    Route::resource('the-loai', AdminCategoryController::class)->parameters(['the-loai' => 'category'])->names('categories')->except('show');
    Route::resource('bo-suu-tap', AdminCollectionController::class)->parameters(['bo-suu-tap' => 'collection'])->names('collections')->except('show');
    Route::resource('more-to-explore', AdminHomeExploreController::class)->parameters(['more-to-explore' => 'tile'])->names('explore')->except('show');
    Route::resource('banner', AdminBannerController::class)->parameters(['banner' => 'banner'])->names('banners')->except('show');
    Route::get('/gioi-thieu', [AboutPageController::class, 'edit'])->name('about.edit');
    Route::post('/gioi-thieu', [AboutPageController::class, 'update'])->name('about.update');
    Route::post('/gioi-thieu/khoi-phuc-mac-dinh', [AboutPageController::class, 'resetDefault'])->name('about.reset');
    Route::get('/tai-khoan', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/tai-khoan', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/trang-chinh-sach', [PolicyPageController::class, 'edit'])->name('policy-pages.edit');
    Route::post('/trang-chinh-sach', [PolicyPageController::class, 'update'])->name('policy-pages.update');
    Route::post('/trang-chinh-sach/khoi-phuc-mac-dinh', [PolicyPageController::class, 'resetDefault'])->name('policy-pages.reset');
    Route::get('/cau-hinh', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/cau-hinh', [SettingController::class, 'update'])->name('settings.update');
});
