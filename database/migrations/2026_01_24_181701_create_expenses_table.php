<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // who paid
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->string('category')->default('other');
            $table->date('date');
            $table->boolean('is_recurring')->default(false);
            $table->unsignedTinyInteger('recurring_day')->nullable(); // day of month (1-31)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
