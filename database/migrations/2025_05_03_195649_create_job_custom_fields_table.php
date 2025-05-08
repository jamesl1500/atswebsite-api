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
        Schema::create('job_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->enum('type', ['text', 'textarea', 'select', 'checkbox', 'file']);
            $table->text('options')->nullable(); // JSON field for select options
            $table->boolean('required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_custom_fields');
        Schema::table('job_custom_fields', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
        });
    }
};
