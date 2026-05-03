<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hashtags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // without #
            $table->unsignedInteger('usage_count')->default(0);
            $table->unsignedInteger('today_count')->default(0);
            $table->boolean('is_trending')->default(false);
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('is_trending');
            $table->index('today_count');
        });

        Schema::create('hashtaggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hashtag_id')->constrained()->cascadeOnDelete();
            $table->morphs('hashtaggable'); // posts, anonymous_posts, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hashtaggables');
        Schema::dropIfExists('hashtags');
    }
};
