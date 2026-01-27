<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('portal_tutorials', function (Blueprint $table) {
      $table->id();
      $table->string('title', 190);
      $table->string('slug', 220)->unique();
      $table->string('category', 40)->default('tributario');
      $table->string('excerpt', 300)->nullable();
      $table->longText('body')->nullable();

      $table->string('cover_image_url', 2000)->nullable();
      $table->string('youtube_url', 2000); // ✅ se guarda pero NO se muestra
      $table->string('duration_label', 20)->nullable(); // "12:00"

      $table->enum('status', ['draft','published'])->default('draft');
      $table->timestamp('published_at')->nullable();

      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();

      $table->timestamps();

      $table->index(['status','published_at']);
      $table->index('category');
    });
  }

  public function down(): void {
    Schema::dropIfExists('portal_tutorials');
  }
};
