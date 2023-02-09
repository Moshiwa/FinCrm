<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('pipeline_id')->constrained();
            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained();

            $table->timestamps();
        });

        SpaceService::addBaseModelForeignIdMigration('deals', 'responsible_id', 'users', 'cascade', false);

    }

    public function down()
    {
        Schema::dropIfExists('deals');
    }
};
