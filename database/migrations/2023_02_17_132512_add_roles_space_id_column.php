<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tableNames = config('permission.table_names');

        if (!Schema::hasColumn($tableNames['model_has_permissions'], 'space_id')) {
            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
                $table->foreignId('space_id')->nullable()->constrained('spaces')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        $tableNames = config('permission.table_names');
        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table)  {
            $table->dropColumn('space_id');
        });
    }
};
