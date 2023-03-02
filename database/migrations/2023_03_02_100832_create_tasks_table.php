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
            $table->text('description')->default('');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('status');
            $table->timestamps();
        });

        SpaceService::addBaseModelForeignIdMigration(
            'tasks',
            'manager_id',
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
