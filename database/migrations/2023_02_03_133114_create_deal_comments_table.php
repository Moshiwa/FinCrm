<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deal_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('type');
            $table->text('title')->default('');
            $table->text('content')->default('');
            $table->timestamps();
        });

        SpaceService::addBaseModelForeignIdMigration('deal_comments', 'author_id', 'users', 'cascade', true);

    }

    public function down()
    {
        Schema::dropIfExists('deal_comments');
    }
};
