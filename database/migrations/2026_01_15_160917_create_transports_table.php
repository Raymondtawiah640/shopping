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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_id');
            $table->string('name');
            $table->text('description');
            $table->string('transport_type'); // car, bus, boat, plane
            $table->string('departure_location');
            $table->string('arrival_location');
            $table->decimal('price_per_person', 10, 2);
            $table->integer('capacity');
            $table->integer('available_seats');
            $table->datetime('departure_time');
            $table->datetime('arrival_time');
            $table->json('images')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->onDelete('cascade');
            $table->index(['departure_location', 'arrival_location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
