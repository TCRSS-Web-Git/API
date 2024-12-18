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
        Schema::create('subdistricts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->string('name_th');
            $table->string('name_en')->nullable();
            //            $table->string('latitude')->nullable();
            //            $table->string('longitude')->nullable();
            $table->string('zip')->nullable();
            $table->string('sid')->nullable();
            $table->timestamps();

            $table->foreign('district_id')
                ->references('id')->on('districts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subdistricts');
    }
};
