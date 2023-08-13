<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_mailgun', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->boolean('enabled')->default(false);
            $table->boolean('distribute_order')->default(false);
            $table->boolean('refund_order')->default(false);
            $table->string('name');
            $table->string('domain');
            $table->string('endpoint');
            $table->string('secret');
            $table->string('api_key');
            $table->string('order_template');
            $table->string('order_subject');
            $table->string('refund_template');
            $table->string('refund_subject');
            $table->string('from');
            $table->string('sandbox_to');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_mailgun');
    }
};
