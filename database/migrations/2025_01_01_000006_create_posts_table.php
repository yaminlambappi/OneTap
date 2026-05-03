<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('community_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // text, image, meme, poll, spotted, question, hot_take, challenge, event_shout, voice_drop
            $table->text('body')->nullable();
            $table->json('media')->nullable(); // [{url, type, width, height, thumbnail}]
            $table->string('visibility')->default('public'); // public, campus, community, friends
            $table->boolean('is_anonymous')->default(false);
            $table->string('anonymous_alias')->nullable();
            $table->string('anonymous_avatar_seed')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('reaction_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->unsignedInteger('share_count')->default(0);
            $table->unsignedInteger('boost_score')->default(0); // feed ranking score
            $table->float('velocity_score')->default(0); // reactions per minute
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_nsfw')->default(false);
            $table->boolean('is_removed')->default(false);
            $table->string('removed_reason')->nullable();
            $table->timestamp('expires_at')->nullable(); // for ephemeral posts
            $table->timestamps();

            $table->index('user_id');
            $table->index('campus_id');
            $table->index('community_id');
            $table->index(['lat', 'lng']);
            $table->index('is_trending');
            $table->index('boost_score');
            $table->index('created_at');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
