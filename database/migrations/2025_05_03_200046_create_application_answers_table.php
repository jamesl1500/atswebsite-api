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
        Schema::create('application_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_custom_field_id')->constrained()->onDelete('cascade');
            $table->string('answer'); // Assuming this is a string, adjust as necessary
            $table->timestamps();
            $table->softDeletes(); // Soft delete for application answers
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_answers', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropForeign(['job_custom_field_id']);
        });
        Schema::dropIfExists('application_answers');
    }
};
