<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#ffffff');;
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_stages');
    }
};