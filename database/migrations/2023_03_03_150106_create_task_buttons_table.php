<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_buttons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('task_stage_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('color')->default('default');
            $table->string('icon')->default('angle-double-right');
            $table->boolean('is_default')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_buttons');
    }
};
