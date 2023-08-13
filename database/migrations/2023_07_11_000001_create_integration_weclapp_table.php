<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_weclapp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->boolean('enabled')->default(false);
            $table->boolean('receive_inventory')->default(false);
            $table->boolean('distribute_order')->default(false);
            $table->string('name');
            $table->string('url');
            $table->string('api_token');
            $table->string('article_category_id')->nullable();
            $table->string('distribution_channel')->nullable();
            $table->string('warehouse_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_weclapp');
    }
};
