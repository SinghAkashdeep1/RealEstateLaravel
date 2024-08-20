<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('price');
            $table->string('description');
            $table->string('bedrooms');
            $table->string('bathrooms');
            $table->string('area');
            $table->string('property_type');
            $table->string('property_sub_type');
            $table->string('listing_type');
            $table->string('image');
            $table->string('user_id');
            $table->string('created_by')->nullable();
            $table->string('status')->default(1);
            $table->string('type')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
};
