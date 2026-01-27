<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sold', 'deleted_at']);
            $table->boolean('is_perishable')->default(false);
            $table->integer('preparation_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->integer('sold')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->dropColumn(['is_perishable', 'preparation_time']);
        });
    }
};
