<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_events', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')
                ->constrained();
            $table->string('action');
            $table->json('data');
            $table->timestamp('created_at', 0)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_events');
    }
};
