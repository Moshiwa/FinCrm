<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_button_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_button_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('task_stage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_button_stages');
    }
};
