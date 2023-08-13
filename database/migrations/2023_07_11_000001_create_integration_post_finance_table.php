<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_post_finance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->boolean('enabled')->default(false);
            $table->string('name');
            $table->string('space_id');
            $table->string('user_id');
            $table->string('secret');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_post_finance');
    }
};
