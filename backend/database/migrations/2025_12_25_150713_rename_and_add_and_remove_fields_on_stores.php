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
        Schema::table('stores', function (Blueprint $table) {

            $table->renameColumn('owner_id', 'user_id');
            $table->renameColumn('chave_pix', 'pix_key');

            $table->dropColumn(['whatsapp', 'deleted_at']);

            $table->text('schedules');
            $table->boolean('is_open')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->integer('delivery_time_km')->nullable();
            $table->decimal('dynamic_freight_km')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {

            $table->renameColumn('user_id', 'owner_id');
            $table->renameColumn('pix_key', 'chave_pix');

            $table->string('whatsapp')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->dropColumn([
                'schedules',
                'is_open',
                'is_delivered',
                'delivery_time_km',
                'dynamic_freight_km',
            ]);
        });
    }

};
