<?php

use App\Enums\Transaction\TransactionStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->foreignUuid('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->uuidMorphs('integration');
            $table->enum('status', array_map(fn(TransactionStatusEnum $status) => $status->value, TransactionStatusEnum::cases()));
            $table->string('method');
            $table->string('resource_id');
            $table->json('resource_data')->nullable();
            $table->uuid('webhook_id')->nullable();
            $table->string('checkout_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
