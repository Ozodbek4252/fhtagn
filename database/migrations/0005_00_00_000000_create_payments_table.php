<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('type', ['card', 'credit']);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('credit_provider')->nullable();
            $table->tinyInteger('credit_term_months')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
