<?php

use App\Enums\FieldsEntitiesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('string');
            $table->string('name');
            $table->string('entity');
            $table->json('options')->nullable();
            $table->boolean('is_active')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fields');
    }
};
