<?php

use App\Enums\Address\SalutationEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->foreignUuid('customer_id')
                ->constrained();
            $table->boolean('primary')->default(false);
            $table->string('company')->nullable();
            $table->enum('salutation', array_column(SalutationEnum::cases(), 'value'));
            $table->string('first_name');
            $table->string('last_name');
            $table->string('street');
            $table->string('number');
            $table->string('additional')->nullable();
            $table->string('postal_code');
            $table->string('city');
            $table->char('country_code', 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
