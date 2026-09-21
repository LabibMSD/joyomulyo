<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_order_id')->constrained('service_orders')->restrictOnDelete();
            $table->decimal('amount', 15, 2);

            $table->enum('payment_method', [
                'CASH',
                'TRANSFER',
                'QRIS',
            ]);

            $table->timestamp('paid_at');
            $table->text('notes')->nullable();

            $table->enum('status', [
                'ACTIVE',
                'VOID',
            ])->default('ACTIVE');

            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->text('void_reason')->nullable();

            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
