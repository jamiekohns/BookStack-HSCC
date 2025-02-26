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
        Schema::create('playground', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('data');
            $table->integer('user_id')->unsigned(); 
            $table->integer('source_id')->unsigned()->nullable();
            $table->tinyInteger('published', false, true)->default(false);
            $table->tinyInteger('locked', false, true)->default(false);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('playground')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playground');
    }
};
