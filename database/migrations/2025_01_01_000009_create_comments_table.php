<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('commentable'); // posts, confessions
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_anonymous')->default(false);
            $table->string('anonymous_alias')->nullable();
            $table->string('anonymous_avatar_seed')->nullable();
            $table->unsignedInteger('reaction_count')->default(0);
            $table->unsignedInteger('reply_count')->default(0);
            $table->boolean('is_removed')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
