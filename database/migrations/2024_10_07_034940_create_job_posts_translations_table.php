<?php

declare(strict_types=1);

use App\Models\Blog;
use App\Models\JobPost;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(JobPost::class, 'item_id')
                ->constrained('jobs')
                ->cascadeOnDelete();

            $table->string('locale');

            $table->string('title')->nullable();
            $table->mediumText('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->unique(['item_id', 'locale']);
        });

        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE job_translations ADD FULLTEXT job_translations_fulltext (title, body) WITH PARSER ngram');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_translations');
    }
};
