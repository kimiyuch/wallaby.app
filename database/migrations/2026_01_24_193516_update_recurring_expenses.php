<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('recurring_day');
            $table->date('end_date')->nullable()->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->integer('recurring_day')->nullable();
            $table->dropColumn('end_date');
        });
    }
};
