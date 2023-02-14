<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deal_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('field_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('value')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deal_fields');
    }
};
