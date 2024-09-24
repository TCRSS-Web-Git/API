<?php

declare(strict_types=1);

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Category::class, 'item_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->string('locale');

            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->unique(['item_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};
