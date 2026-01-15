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
        Schema::create('hospitality_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id')->unique();
            $table->string('customer_id');
            $table->string('vendor_id');
            $table->enum('service_type', ['hotel', 'restaurant', 'transport', 'tour']);
            $table->unsignedBigInteger('service_id'); // ID of the specific service
            $table->integer('number_of_guests')->default(1);
            $table->datetime('check_in_date')->nullable();
            $table->datetime('check_out_date')->nullable();
            $table->datetime('booking_date')->nullable();
            $table->text('special_requests')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('vendor_notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->onDelete('cascade');
            $table->index(['customer_id', 'vendor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitality_bookings');
    }
};
