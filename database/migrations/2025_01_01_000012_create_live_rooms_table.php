<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique(); // short join code
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('topic');
            $table->string('type')->default('open'); // open, campus, private
            $table->unsignedSmallInteger('participant_count')->default(0);
            $table->unsignedSmallInteger('max_participants')->default(50);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('campus_id');
        });

        Schema::create('live_room_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();

            $table->unique(['live_room_id', 'user_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->string('type')->default('text'); // text, reaction, system
            $table->boolean('is_anonymous')->default(false);
            $table->string('anonymous_alias')->nullable();
            $table->string('anonymous_avatar_seed')->nullable();
            $table->timestamps();

            $table->index('live_room_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('live_room_participants');
        Schema::dropIfExists('live_rooms');
    }
};
