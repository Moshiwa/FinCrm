<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settingables', function (Blueprint $table) {
            $table->id();
            $table->morphs('settingable');
            $table->foreignId('setting_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_enable')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settingables');
    }
};
