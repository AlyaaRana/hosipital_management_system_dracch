<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('files', function (Blueprint $table) {
        $table->id();
        $table->string('path');
        $table->string('filename');
        $table->string('mime_type');
        $table->integer('size');
        $table->unsignedBigInteger('fileable_id');
        $table->string('fileable_type');
        $table->foreignId('uploaded_by')->constrained('users');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};

