<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('type', ['pickup', 'address']);
            $table->foreignId('pickup_point_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('building', 50)->nullable();
            $table->string('apartment', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
