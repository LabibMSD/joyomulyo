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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();

            $table->string('service_number', 30)->unique();
            $table->foreignId('vehicle_id')->constrained('vehicles')->restrictOnDelete();

            $table->enum('status', [
                'CHECKING',
                'WAITING_DECISION',
                'WORKING',
                'WAITING_PART',
                'COMPLETED',
                'DECLINED',
            ])->default('CHECKING');

            $table->text('complaint')->nullable();
            $table->text('notes')->nullable();

            $table->string('invoice_number', 30)->nullable()->unique();
            $table->timestamp('invoiced_at')->nullable();
            $table->foreignId('invoiced_by')->nullable()->constrained('users')->restrictOnDelete();

            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
