<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('button_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('button_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('stage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('pipeline_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->boolean('comment')->default(false);
        });

        SpaceService::addBaseModelForeignIdMigration(
            'button_actions',
            'responsible_id',
            'users'
        );
    }

    public function down()
    {
        Schema::dropIfExists('button_actions');
    }
};
