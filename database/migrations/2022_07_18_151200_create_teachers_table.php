<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->bigInteger('phone_number')->unique();
            $table->string('email', 255)->unique();
            $table->string('identification_type');
            $table->bigInteger('identification_number');
            $table->foreignId('center_id')->references('id')->on('centers')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teachers');
    }
};
