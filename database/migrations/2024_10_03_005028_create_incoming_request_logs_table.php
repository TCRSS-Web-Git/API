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
        Schema::create('incoming_request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method');
            $table->string('ip');
            $table->string('uri')->nullable();
            $table->json('header')->nullable();
            $table->mediumText('body')->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->json('response_header')->nullable();
            $table->mediumText('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_request_logs');
    }
};
