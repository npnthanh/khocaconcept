<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@trungbaysach.local')],
            ['name' => env('ADMIN_NAME', 'Quản trị viên'), 'password' => Hash::make(env('ADMIN_PASSWORD', 'doi-mat-khau-ngay'))]
        );

        $categories = collect([
            'Văn học Việt Nam', 'Văn học nước ngoài', 'Thiếu nhi', 'Kỹ năng sống',
            'Lịch sử', 'Khoa học', 'Kinh tế', 'Nghệ thuật',
        ])->mapWithKeys(fn (string $name, int $index) => [$name => Category::query()->firstOrCreate(
            ['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $index]
        )]);

        foreach ([
            ['Dế Mèn phiêu lưu ký', 'Tô Hoài', 'Kim Đồng', 2017, 'Thiếu nhi', '9786042098724'],
            ['Tuổi thơ dữ dội', 'Phùng Quán', 'Kim Đồng', 2020, 'Văn học Việt Nam', '9786042214223'],
            ['Nhà giả kim', 'Paulo Coelho', 'Hội Nhà Văn', 2022, 'Văn học nước ngoài', '9786043687354'],
            ['Đắc nhân tâm', 'Dale Carnegie', 'Tổng hợp TP.HCM', 2021, 'Kỹ năng sống', '9786045886786'],
            ['Sapiens: Lược sử loài người', 'Yuval Noah Harari', 'Thế Giới', 2023, 'Lịch sử', '9786043657685'],
            ['Vũ trụ', 'Carl Sagan', 'Tri Thức', 2022, 'Khoa học', '9786043404630'],
        ] as $data) {
            [$title, $author, $publisher, $year, $category, $isbn] = $data;
            $book = Book::query()->updateOrCreate(
                ['isbn' => $isbn],
                ['title' => $title, 'slug' => Str::slug($title), 'author' => $author, 'publisher' => $publisher, 'published_year' => $year, 'volume_count' => 1, 'page_count' => 280, 'dimensions' => '13 x 20.5 cm', 'summary' => 'Thông tin giới thiệu mẫu. Bạn có thể cập nhật nội dung đầy đủ trong trang quản trị.']
            );
            $book->categories()->sync([$categories[$category]->id]);
        }
    }
}
