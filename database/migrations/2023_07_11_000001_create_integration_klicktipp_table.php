<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('integration_klicktipp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->boolean('enabled')->default(false);
            $table->boolean('distribute_order');
            $table->string('name');
            $table->string('user_name');
            $table->string('developer_key');
            $table->string('customer_key');
            $table->string('service');
            $table->string('tag_prefix');
            $table->json('tags')->nullable();
            $table->json('tags_coupons')->nullable();
            $table->json('tags_periods')->nullable();
            $table->json('tags_new_customer')->nullable();
            $table->json('tags_products')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('integration_klicktipp');
    }
};
