<?php

namespace App\Services\Space;

use App\Models\Space;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SpaceService
{
    public static $spaces;
    public static $sessionName = 'space';
    public static $mainCode = 'main';
    public static $currentCode;
    public static $model;

    public static function getSpaces(): array
    {
        $spaces = collect([self::$mainCode => 'Основная']);

        if (Schema::connection(self::getDefaultConnectionName())->hasTable('spaces')) {
            $spaces = $spaces->merge(
                Space::query()
                    ->where('active', true)
                    ->pluck('name', 'code')
            );
        }

        return $spaces->toArray();
    }

    public static function getCurrentSpaceModel($force = false): Space
    {
        $spaceCode = self::getCurrentSpaceCode();
        if (!self::$model || $force) {
            self::$model = Space::query()->where('code', $spaceCode)->get()->first();
        }
        return self::$model;
    }

    public static function getCurrentSpaceCode()
    {
        if(app()->runningInConsole()) {
            $currentSpace = self::$currentCode;
        } else {
            $currentSpace = session()->get(self::$sessionName);
        }

        $spaces = self::getSpaces();
        if (!array_key_exists($currentSpace, $spaces)) {
            $currentSpace = null;
        }
        if (!$currentSpace) {
            $currentSpace = self::$mainCode;
            self::setCurrentSpaceCode($currentSpace);
        }

        return $currentSpace;
    }

    public static function setCurrentSpaceCode($space): void
    {
        if(app()->runningInConsole()) {
            self::$currentCode = $space;
        } else {
            session()->put(self::$sessionName, $space);
        }

        self::getCurrentSpaceModel(true);
        self::setDefaultDatabaseConnection();
    }

    public static function prepareAllUploadDirectories(): void
    {
        $spaces = SpaceService::getSpaces();
        $baseUploadDirectory = base_path('public/uploads');

        foreach ($spaces as $spaceCode => $spaceName) {
            if($spaceCode == 'main') continue;
            $directory = self::getSpaceUploadDirectory($spaceCode);
            if(!file_exists($directory)) {
                shell_exec("mkdir $directory && cp $baseUploadDirectory/.htaccess $directory/.htaccess");
            }
        }
    }

    public static function removeSpaceUploadDirectory($spaceCode): void
    {
        $directoryFull = self::getSpaceUploadDirectory($spaceCode);
        $directory = self::getSpaceUploadDirectory($spaceCode, false);
        shell_exec("rm -rf $directoryFull");
        shell_exec("rm -rf ".base_path('storage/app/public/'.$directory));
    }

    public static function prepareAllSpaceConnections(): void
    {
        $connections = [];
        $spaces = SpaceService::getSpaces();
        foreach ($spaces as $spaceCode => $spaceName) {
            if($spaceCode == 'main') continue;
            $connections[self::getSpaceConnectionName($spaceCode)] = SpaceService::prepareSpaceConnectionOptions($spaceCode);
            self::addSpaceConnections($spaceCode);
        }

        file_put_contents(base_path('config/database_spaces.php'), "<?php\n\nreturn " . var_export($connections, true) . "\n\n?>");
    }

    public static function addSpaceConnections($space): void
    {
        config(['database.connections.' . self::getSpaceConnectionName($space) => self::prepareSpaceConnectionOptions($space)]);
    }

    public static function setDefaultDatabaseConnection(): void
    {
        $currentSpace = self::getCurrentSpaceCode();
        config(['database.default' => self::getSpaceConnectionName($currentSpace)]);
    }

    public static function setDefaultUploadDiskPath(): void
    {
        $currentSpace = self::getCurrentSpaceCode();
        $disks = config('filesystems.disks');

        if($currentSpace != self::$mainCode) {
            $diskName = 'uploads_' . $currentSpace;
            $disks[$diskName]  = [
                'driver' => 'local',
                'root' => self::getSpaceUploadDirectory($currentSpace),
                'throw' => false,
            ];
            config(['filesystems.disks' => $disks]);
            config(['elfinder.disks' => [$diskName]]);
        }
    }

    public static function getCurrentSpaceUploadDirectory($full = true): string
    {
        $currentSpace = self::getCurrentSpaceCode();
        return self::getSpaceUploadDirectory($currentSpace, $full);
    }

    public static function getSpaceUploadDirectory($space, $full = true): string
    {
        $path = 'uploads'.($space != self::$mainCode ? '_' . $space : '');
        return $full ? base_path($path) : $path;
    }

    public static function prepareSpaceConnectionOptions($space): array
    {
        $allConnections = config('database.connections');
        $defaultConnectionName = self::getDefaultConnectionName();
        $defaultConnection = $allConnections[$defaultConnectionName];
        return array_replace($defaultConnection, ['prefix' => self::getSpaceTablePrefix($space)]);
    }

    protected static function getSpaceTablePrefix($space): string
    {
        return $space != self::$mainCode ? $space . '__' : '';
    }

    public static function getDefaultConnectionName()
    {
        return env('DB_CONNECTION');
    }

    public static function getCurrentConnectionPrefix()
    {
        return config('database.connections')[config('database.default')]['prefix'];
    }

    protected static function getSpaceConnectionName($space): string
    {
        $defaultConnectionName = self::getDefaultConnectionName();
        return $defaultConnectionName . ($space != self::$mainCode ? '_' . $space : '');
    }

    public static function migrateAll($output = false): void
    {
        $spaces = self::getSpaces();
        foreach ($spaces as $spaceCode => $spaceName) {
            self::migrate($spaceCode);
        }
    }

    public static function migrate($space): void
    {
        if (app()->runningInConsole()) {
            dump($space);
        }

        Artisan::call('migrate', [
            '--database' => self::getSpaceConnectionName($space),
            '--force'    => true
        ]);
        if (app()->runningInConsole()) {
            dump(Artisan::output());
        }

        Artisan::call('db:seed', [
            '--database' => self::getSpaceConnectionName($space),
            '--force'    => true
        ]);
        if (app()->runningInConsole()) {
            dump(Artisan::output());
        }
    }

    public static function migrateRollbackAll($step = 1): void
    {
        $spaces = self::getSpaces();
        foreach ($spaces as $spaceCode => $spaceName) {
            self::migrateRollback($spaceCode, $step);
        }
    }

    public static function migrateRollback($space, $step = 1): void
    {
        Artisan::call('migrate:rollback', [
            '--database' => self::getSpaceConnectionName($space),
            '--force'    => true,
            '--step'     => $step
        ]);
        if (app()->runningInConsole()) {
            dump(Artisan::output());
        }
    }

    public static function migrateReset($space): void
    {
        self::addSpaceConnections($space);
        $tables = \DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $prefix = self::getSpaceTablePrefix($space);

        $tables = collect($tables)
            ->filter(function ($table) use ($prefix) {
                if (in_array($table, [
                    'users',
                    'spaces'
                ]))
                    return false;
                return mb_strpos($table, $prefix) === 0;
            })
            ->toArray();

        foreach ($tables as $table) {
            $query = "drop table if exists {$table} cascade;";
            DB::statement($query);
        }
    }

    public static function migrateFresh($space): void
    {
        self::migrate($space);
        self::migrateReset($space);
    }

    public static function addBaseModelForeignIdMigration($tableName, $column, $relationTable, $onDelete = 'set null', bool $nullable = true)
    {
        Schema::table($tableName, function (Blueprint $table) use ($column, $nullable) {
            $table->bigInteger($column, false, true)->nullable($nullable);
        });

        /**
         * @var \Illuminate\Database\PostgresConnection $connection
         */
        $connection = \DB::connection();
        $prefix = $connection->getConfig('prefix');
        $tableName = $prefix . '' . $tableName;

        $query = 'alter table "' . $tableName . '" add constraint "' . $tableName . '_' . $column . '_foreign" foreign key ("' . $column . '") references "' . $relationTable . '" ("id") on delete ' . $onDelete;
        DB::statement($query);
    }
}
