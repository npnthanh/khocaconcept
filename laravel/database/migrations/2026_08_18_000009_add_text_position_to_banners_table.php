<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table): void {
            if (!Schema::hasColumn('banners', 'text_position')) {
                $table->string('text_position', 20)->default('left')->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table): void {
            if (Schema::hasColumn('banners', 'text_position')) {
                $table->dropColumn('text_position');
            }
        });
    }
};
