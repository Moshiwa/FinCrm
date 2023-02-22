<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('button_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('button_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('stage_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('button_stages');
    }
};
