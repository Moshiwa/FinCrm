<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_button_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_button_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('task_stage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('responsible_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('executor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->boolean('comment')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_button_actions');
    }
};
