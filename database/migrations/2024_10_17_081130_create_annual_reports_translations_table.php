<?php

use App\Models\AnnualReport;
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
        Schema::create('annual_report_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(AnnualReport::class, 'item_id')
                ->constrained('annual_reports')
                ->cascadeOnDelete();

            $table->string('locale');
            $table->string('title')->nullable();

            $table->unique(['item_id', 'locale']);

            $table->timestamps();
        });

        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE annual_report_translations ADD FULLTEXT annual_report_translations_fulltext (title) WITH PARSER ngram');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_report_translations');
    }
};
