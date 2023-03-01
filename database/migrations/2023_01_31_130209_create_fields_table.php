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
            $table->foreignId('type_id')
                ->constrained('field_types');
            $table->string('name');
            $table->string('entity');
            $table->json('options')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_required')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fields');
    }
};
