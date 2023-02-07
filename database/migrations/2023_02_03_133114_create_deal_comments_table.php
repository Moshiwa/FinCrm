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
            $table->string('type')->default('text');
            $table->text('content');
            $table->timestamps();
        });

        SpaceService::addBaseModelForeignIdMigration('deal_comments', 'author_id', 'users', 'cascade', false);

    }

    public function down()
    {
        Schema::dropIfExists('deal_comments');
    }
};
