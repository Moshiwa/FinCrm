<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->default('');
            $table->json('data')->nullable();
            $table->boolean('active')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('integrations');
    }
};
