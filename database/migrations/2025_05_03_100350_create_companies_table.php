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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete(); // recruiter/creator
            $table->string('name');
            $table->string('slug')->unique(); // for public URLs like /companies/acme
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('logo_id')->nullable()->constrained('files')->onDelete('set null'); // company logo
            $table->foreignId('cover_id')->nullable()->constrained('files')->onDelete('set null'); // company cover image
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
