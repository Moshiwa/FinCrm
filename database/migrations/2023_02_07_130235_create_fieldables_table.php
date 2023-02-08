<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fieldables', function (Blueprint $table) {
            $table->id();
            $table->morphs('fieldable');
            $table->foreignId('field_id')->constrained();
            $table->string('value')->default('');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fieldables');
    }
};
