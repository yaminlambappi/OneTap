<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('open'); // open, campus, private, geo
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedSmallInteger('radius_meters')->default(500);
            $table->unsignedInteger('member_count')->default(0);
            $table->unsignedInteger('post_count')->default(0);
            $table->boolean('allow_anonymous')->default(true);
            $table->boolean('is_nsfw')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('campus_id');
            $table->index('type');
        });

        Schema::create('community_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member'); // member, mod, admin
            $table->timestamps();

            $table->unique(['community_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_members');
        Schema::dropIfExists('communities');
    }
};
