<?php

use App\Models\Executive;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('executive_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Executive::class, 'item_id')
                ->constrained('executives')
                ->cascadeOnDelete();

            $table->string('locale');
            $table->string('name')->nullable();
            $table->string('position')->nullable();

            $table->unique(['item_id', 'locale']);

            $table->timestamps();
        });

        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE executive_translations ADD FULLTEXT executive_translations_fulltext (name, position) WITH PARSER ngram');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('executive_translations');
    }
};
