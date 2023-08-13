<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_channel_id')
                ->constrained();
            $table->uuidMorphs('vatable');
            $table->char('country_code', 2);
            $table->unsignedSmallInteger('rate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vats');
    }
};
