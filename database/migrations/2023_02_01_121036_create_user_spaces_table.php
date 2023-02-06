<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')
                ->constrained();
            $table->foreignId('user_id')
                ->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_spaces');
    }
};
