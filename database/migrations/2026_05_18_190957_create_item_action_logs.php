<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_action_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name')->nullable(); // store name in case item is deleted
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action', 50); // Created, Updated, Deleted, Restored
            $table->text('details')->nullable(); // what changed
            $table->timestamp('action_at')->nullable();
            $table->timestamps();

            $table->index('item_id');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_action_logs');
    }
};