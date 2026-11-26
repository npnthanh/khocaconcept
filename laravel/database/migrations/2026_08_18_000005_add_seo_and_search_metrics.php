<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->boolean('is_featured')->default(false)->after('is_published')->index();
            $table->unsignedInteger('featured_sort_order')->default(0)->after('is_featured');
            $table->string('meta_title')->nullable()->after('featured_sort_order');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('search_queries', function (Blueprint $table): void {
            $table->unsignedInteger('result_count')->nullable()->after('hits');
            $table->unsignedInteger('no_result_hits')->default(0)->after('result_count');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->dropIndex(['is_featured']);
            $table->dropColumn(['is_featured', 'featured_sort_order', 'meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('search_queries', function (Blueprint $table): void {
            $table->dropColumn(['result_count', 'no_result_hits']);
        });
    }
};
