<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('buttons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('options');
            $table->foreignId('pipeline_id')->constrained();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buttons');
    }
};
