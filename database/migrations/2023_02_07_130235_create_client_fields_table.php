<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('client_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('field_id')->constrained();
            $table->string('value')->default('');
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_fields');
    }
};
