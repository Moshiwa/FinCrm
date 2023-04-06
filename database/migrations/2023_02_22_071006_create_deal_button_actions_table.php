<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deal_button_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_button_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('stage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('pipeline_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('responsible_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('comment')->default(false);
            $table->foreignId('deadline_format_id')->nullable();
            $table->integer('deadline_value')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deal_button_actions');
    }
};
