<?php

use App\Services\Space\SpaceService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $schema = Schema::connection(SpaceService::getDefaultConnectionName());

        $tableNames = config('permission.table_names');

        if (!$schema->hasColumn($tableNames['roles'], 'space_id')) {
            $schema->table($tableNames['roles'], function (Blueprint $table) {
                $table->foreignId('space_id')->nullable()->constrained('spaces')->onDelete('cascade');
            });
        }
        if (!$schema->hasColumn($tableNames['model_has_permissions'], 'space_id')) {
            $schema->table($tableNames['model_has_permissions'], function (Blueprint $table) {
                $table->foreignId('space_id')->nullable()->constrained('spaces')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        $schema = Schema::connection(SpaceService::getDefaultConnectionName());

        $tableNames = config('permission.table_names');

        $schema->table($tableNames['model_has_permissions'], function (Blueprint $table)  {
            $table->dropColumn('space_id');
        });
        $schema->table($tableNames['model_has_permissions'], function (Blueprint $table)  {
            $table->dropColumn('space_id');
        });
    }
};
