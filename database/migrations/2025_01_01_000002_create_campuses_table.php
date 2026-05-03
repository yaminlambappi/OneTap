<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name')->nullable(); // "BUET", "DU", "NSU"
            $table->string('city');
            $table->string('country')->default('BD');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedSmallInteger('radius_meters')->default(2000);
            $table->string('cover_image')->nullable();
            $table->string('color_primary')->default('#6366f1');
            $table->string('color_secondary')->default('#8b5cf6');
            $table->unsignedInteger('member_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lat', 'lng']);
        });

        // Add FK now that campuses exists
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('campus_id')->references('id')->on('campuses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['campus_id']);
        });
        Schema::dropIfExists('campuses');
    }
};
