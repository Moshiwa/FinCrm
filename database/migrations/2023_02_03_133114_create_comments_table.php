<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable');
            $table->string('type');
            $table->string('temp_id')
                ->nullable()
                ->comment('Поле для связи звонка и аудиосообщения из webhook. После привязки должно очищатсья');
            $table->text('title')->default('');
            $table->text('content')->default('');
            $table->timestamps();
        });

        SpaceService::addBaseModelForeignIdMigration('comments', 'author_id', 'users', 'cascade', true);

    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
