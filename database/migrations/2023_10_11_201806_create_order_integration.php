<?php

use App\Enums\Integration\IntegrationResourceStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_integration', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')
                ->constrained();
            $table->uuidMorphs('integration');
            $table->string('resource_id')->nullable();
            $table->enum('status', array_map(fn(IntegrationResourceStatusEnum $status) => $status->value, IntegrationResourceStatusEnum::cases()));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_integration');
    }
};
