<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->string('language', 100)->nullable()->after('publisher');
            $table->string('book_condition', 24)->default('unknown')->after('language');
            $table->string('availability_status', 24)->default('available')->after('book_condition');
            $table->text('search_text')->nullable()->after('additional_information');
            $table->index(['language', 'book_condition', 'availability_status']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->string('search_name')->nullable()->after('name');
        });

        DB::table('books')->orderBy('id')->chunkById(100, function ($books): void {
            foreach ($books as $book) {
                DB::table('books')->where('id', $book->id)->update([
                    'search_text' => $this->normalize(implode(' ', array_filter([
                        $book->title,
                        $book->author,
                        $book->publisher,
                        $book->summary,
                        $book->additional_information,
                        $book->language,
                    ]))),
                ]);
            }
        });

        DB::table('categories')->orderBy('id')->chunkById(100, function ($categories): void {
            foreach ($categories as $category) {
                DB::table('categories')->where('id', $category->id)->update([
                    'search_name' => $this->normalize($category->name),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->dropIndex(['language', 'book_condition', 'availability_status']);
            $table->dropColumn(['language', 'book_condition', 'availability_status', 'search_text']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('search_name');
        });
    }

    private function normalize(?string $value): string
    {
        return Str::ascii(Str::lower(Str::squish((string) $value)));
    }
};
