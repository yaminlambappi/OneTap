<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('confessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // always hidden
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('community_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->string('category')->default('general'); // crush, academic, social, rant, secret
            $table->string('mood')->nullable(); // 😭😍🔥💀
            $table->string('anonymous_alias'); // "ShadowPanda99"
            $table->string('anonymous_avatar_seed');
            $table->unsignedInteger('reaction_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->float('velocity_score')->default(0);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_removed')->default(false);
            $table->string('removed_reason')->nullable();
            $table->timestamps();

            $table->index('campus_id');
            $table->index('is_trending');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confessions');
    }
};
