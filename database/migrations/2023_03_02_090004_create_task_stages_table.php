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
            $table->integer('deadline');
            $table->foreignId('deadline_format_id')
                ->constrained();
            $table->integer('parent_id')->nullable();
            $table->integer('lft')->default(0);
            $table->integer('rgt')->default(0);
            $table->integer('depth')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_stages');
    }
};
