<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_executors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
        });

        SpaceService::addBaseModelForeignIdMigration(
            'task_executors',
            'executor_id',
            'users',
            'cascade',
            false
        );
    }

    public function down()
    {
        Schema::dropIfExists('task_executors');
    }
};
