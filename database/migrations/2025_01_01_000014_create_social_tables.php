<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Friendships / connections
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, accepted, blocked
            $table->timestamps();

            $table->unique(['sender_id', 'receiver_id']);
            $table->index('receiver_id');
            $table->index('status');
        });

        // Blocks
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['blocker_id', 'blocked_id']);
        });

        // User streaks
        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('current_streak')->default(0);
            $table->unsignedSmallInteger('longest_streak')->default(0);
            $table->unsignedInteger('total_posts')->default(0);
            $table->unsignedInteger('total_reactions_given')->default(0);
            $table->unsignedInteger('total_reactions_received')->default(0);
            $table->unsignedInteger('total_comments')->default(0);
            $table->date('last_active_date')->nullable();
            $table->timestamps();
        });

        // Social scores / gamification
        Schema::create('social_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_score')->default(0);
            $table->unsignedSmallInteger('weekly_score')->default(0);
            $table->unsignedSmallInteger('campus_rank')->default(0);
            $table->unsignedSmallInteger('global_rank')->default(0);
            $table->json('badges')->nullable(); // ["early_adopter","chaos_king","confession_lord"]
            $table->json('achievements')->nullable();
            $table->timestamps();
        });

        // User presence (realtime)
        Schema::create('user_presence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_online')->default(false);
            $table->string('status')->default('offline'); // online, away, busy, ghost
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index(['lat', 'lng']);
            $table->index('is_online');
        });

        // Trending topics
        Schema::create('trending_topics', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->string('type')->default('hashtag'); // hashtag, keyword, event
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('post_count')->default(0);
            $table->unsignedInteger('reaction_count')->default(0);
            $table->date('trending_date');
            $table->timestamps();

            $table->index(['campus_id', 'trending_date']);
            $table->index('score');
        });

        // Nearby feeds cache
        Schema::create('nearby_feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('feedable'); // posts, confessions, events
            $table->float('distance_km')->default(0);
            $table->float('relevance_score')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('user_id');
            $table->index('expires_at');
        });

        // Reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reportable');
            $table->string('reason'); // spam, harassment, nsfw, misinformation, other
            $table->text('details')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, actioned, dismissed
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('nearby_feeds');
        Schema::dropIfExists('trending_topics');
        Schema::dropIfExists('user_presence');
        Schema::dropIfExists('social_scores');
        Schema::dropIfExists('user_streaks');
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('friendships');
    }
};
