<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type'); // cafe, campus_spot, park, mall, street, event_venue
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('city');
            $table->string('address')->nullable();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cover_image')->nullable();
            $table->unsignedInteger('checkin_count')->default(0);
            $table->unsignedInteger('post_count')->default(0);
            $table->unsignedSmallInteger('vibe_score')->default(0); // realtime activity level
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lat', 'lng']);
            $table->index('is_trending');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
