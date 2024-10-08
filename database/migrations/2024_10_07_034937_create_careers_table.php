<?php

use App\Models\Category;
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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Category::class, 'location_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->foreignIdFor(Category::class, 'department_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->foreignIdFor(Category::class, 'type_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
