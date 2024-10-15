<?php

use App\Models\ProductAndService;
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
        Schema::create('product_and_service_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(ProductAndService::class, 'item_id')
                ->constrained('product_and_services')
                ->cascadeOnDelete();

            $table->string('locale');
            $table->string('title')->nullable();

            $table->unique(['item_id', 'locale']);

            $table->timestamps();
        });

        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE product_and_service_translations ADD FULLTEXT product_and_service_translations_fulltext (title) WITH PARSER ngram');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_and_service_translations');
    }
};
