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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_id');
            $table->string('name');
            $table->text('description');
            $table->string('location');
            $table->string('city');
            $table->string('country');
            $table->decimal('price_per_night', 10, 2);
            $table->integer('total_rooms');
            $table->integer('available_rooms');
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->onDelete('cascade');
            $table->index(['city', 'country']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
