<?php

use App\Models\BoardDirector;
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
        Schema::create('board_director_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(BoardDirector::class, 'item_id')
                ->constrained('board_directors')
                ->cascadeOnDelete();

            $table->string('locale');
            $table->string('name')->nullable();
            $table->string('position')->nullable();

            $table->unique(['item_id', 'locale']);

            $table->timestamps();
        });

        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE board_director_translations ADD FULLTEXT board_director_translations_fulltext (name, position) WITH PARSER ngram');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_director_translations');
    }
};
