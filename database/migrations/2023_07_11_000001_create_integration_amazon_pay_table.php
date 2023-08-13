<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('integration_amazon_pay', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->boolean('enabled')->default(false);
            $table->string('name');
            $table->string('merchant_account_id');
            $table->string('public_key_id');
            $table->text('private_key');
            $table->string('region');
            $table->string('store_id');
            $table->boolean('sandbox');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('integration_amazon_pay');
    }
};
