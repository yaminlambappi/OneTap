<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('username')->unique();
            $table->string('phone', 20)->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->json('vibe_tags')->nullable(); // ["chill","chaotic","artsy"]
            $table->unsignedSmallInteger('reputation_score')->default(0);
            $table->unsignedSmallInteger('mystery_score')->default(0);
            $table->unsignedSmallInteger('influence_score')->default(0);
            $table->unsignedSmallInteger('chaos_score')->default(0);
            $table->string('local_rank')->default('newcomer'); // newcomer,rising,known,legend
            $table->string('influence_level')->default('ghost'); // ghost,spark,flame,inferno
            $table->boolean('is_anonymous_mode')->default(false);
            $table->string('anonymous_alias')->nullable(); // "MysteriousFox42"
            $table->string('anonymous_avatar_seed')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_until')->nullable();
            $table->decimal('last_lat', 10, 7)->nullable();
            $table->decimal('last_lng', 10, 7)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['last_lat', 'last_lng']);
            $table->index('campus_id');
            $table->index('reputation_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
