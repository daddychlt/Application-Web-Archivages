<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up() {
        Schema::create('document_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->references('id')->on('documents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('tagger')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->longText('message');
            $table->boolean('new')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_user');
    }
};
