<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $schema = Schema::connection(SpaceService::getDefaultConnectionName());
        if($schema->hasTable('user_spaces'))
            return;

        Schema::create('user_spaces', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        SpaceService::addBaseModelForeignIdMigration('user_spaces', 'user_id', 'users', 'cascade', false);
        SpaceService::addBaseModelForeignIdMigration('user_spaces', 'space_id', 'spaces', 'cascade', false);


        Schema::table('user_spaces', function (Blueprint $table) {
            $table->unique(['user_id','space_id']);
        });
    }

    public function down()
    {
        Schema::connection(SpaceService::getDefaultConnectionName())->dropIfExists('user_spaces');
    }
};
