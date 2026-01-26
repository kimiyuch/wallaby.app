<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->foreignId('settled_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->string('balance_owed_by')->nullable(); // user name who owed
            $table->timestamps();

            $table->unique(['space_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
