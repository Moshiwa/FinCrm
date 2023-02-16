<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('stage_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings_stages');
    }
};
