<?php

use App\Models\Award;
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
        Schema::create('award_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Award::class, 'item_id')
                ->constrained('awards')
                ->cascadeOnDelete();

            $table->string('locale');
            $table->string('title')->nullable();
            $table->mediumText('description')->nullable();

            $table->unique(['item_id', 'locale']);

            $table->timestamps();
        });

        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE award_translations ADD FULLTEXT award_translations_fulltext (title, description) WITH PARSER ngram');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('award_translations');
    }
};
