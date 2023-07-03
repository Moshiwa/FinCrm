<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->integer('parent_id')->nullable();
            $table->integer('lft')->default(0);
            $table->integer('rgt')->default(0);
            $table->integer('depth')->default(0);
        });
    }

    public function down()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn([
                "parent_id",
                "lft",
                "rgt",
                "depth"
            ]);
        });
    }
};
