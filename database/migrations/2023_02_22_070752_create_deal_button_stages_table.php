<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deal_button_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_button_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('stage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deal_button_stages');
    }
};
