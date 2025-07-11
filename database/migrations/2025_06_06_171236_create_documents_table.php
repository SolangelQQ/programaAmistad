<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category'); // Administrativo, Operativo, Capacitación, Reportes
            $table->string('chapter')->nullable(); // Manual de inducción, Formularios, etc.
            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('file_size'); // en bytes
            $table->string('file_type'); // pdf, doc, xls, etc.
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['category', 'chapter']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};