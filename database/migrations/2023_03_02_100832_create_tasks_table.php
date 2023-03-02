<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('task_stage_id')->constrained();
            $table->text('description')->nullable();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('status');
            $table->timestamps();
        });

        SpaceService::addBaseModelForeignIdMigration(
            'tasks',
            'responsible_id',
            'users',
            'cascade',
            false
        );

        SpaceService::addBaseModelForeignIdMigration(
            'tasks',
            'manager_id',
            'users',
            'cascade',
            false
        );

        SpaceService::addBaseModelForeignIdMigration(
            'tasks',
            'executor_id',
            'users',
            'cascade',
            false
        );
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
