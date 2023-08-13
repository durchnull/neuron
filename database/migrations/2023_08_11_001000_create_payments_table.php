<?php

use App\Enums\Payment\PaymentMethodEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->uuidMorphs('integration');
            $table->boolean('enabled');
            $table->string('name');
            $table->enum('method', array_map(fn(PaymentMethodEnum $paymentMethod) => $paymentMethod->value, PaymentMethodEnum::cases()));
            $table->string('description');
            $table->unsignedTinyInteger('position');
            $table->boolean('default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
