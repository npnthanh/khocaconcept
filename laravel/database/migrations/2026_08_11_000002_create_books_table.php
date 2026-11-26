<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->unsignedSmallInteger('published_year')->nullable();
            $table->unsignedSmallInteger('volume_count')->nullable();
            $table->string('isbn', 32)->nullable()->unique();
            $table->unsignedInteger('page_count')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('summary')->nullable();
            $table->text('additional_information')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();

            $table->index(['title', 'author']);
            $table->index('published_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
