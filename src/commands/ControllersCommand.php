<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/08/2016
 * Time: 10:59 PM
 */

namespace Invigor\UM;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ControllersCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'um:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration following the UM specifications.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->laravel->view->addNamespace('um', substr(__DIR__, 0, -8) . 'views');

        $groupsTable = Config::get('um.groups_table');
        $groupUserTable = Config::get('um.group_user_table');
        $rolesTable = Config::get('um.roles_table');
        $roleUserTable = Config::get('um.role_user_table');
        $permissionsTable = Config::get('um.permissions_table');
        $permissionRoleTable = Config::get('um.permission_role_table');

        $this->line('');
        $this->info("Tables: $groupsTable, $groupUserTable, $rolesTable, $roleUserTable, $permissionsTable, $permissionRoleTable");

        $message = "A migration that creates '$groupsTable', '$groupUserTable', '$rolesTable', '$roleUserTable', '$permissionsTable', '$permissionRoleTable'" .
            " tables will be created in database/migrations directory";

        $this->comment($message);
        $this->line('');

        if ($this->confirm("Proceed with the migration creation? [Yes|no]", "Yes")) {

            $this->line('');

            $this->info("Creating migration...");
            if ($this->createMigration($groupsTable, $groupUserTable, $rolesTable, $roleUserTable, $permissionsTable, $permissionRoleTable)) {

                $this->info("Migration successfully created!");
            } else {
                $this->error(
                    "Couldn't create migration.\n Check the write permissions" .
                    " within the database/migrations directory."
                );
            }

            $this->line('');

        }
    }

    /**
     * Create the migration.
     * @param $className
     * @return bool
     * @internal param $groupsTable
     * @internal param $rolesTable
     * @internal param $roleUserTable
     * @internal param $permissionsTable
     * @internal param $permissionRoleTable
     * @internal param string $name
     */
    protected function createControllers($className)
    {
        $migrationFile = app_path("Http/Controllers/um/") . $className . ".php";

        $userKeyName = (new $userModel())->getKeyName();

        $data = compact('groupsTable', 'groupUserTable', 'rolesTable', 'roleUserTable', 'permissionsTable', 'permissionRoleTable', 'usersTable', 'userKeyName');

        $output = $this->laravel->view->make('um::generators.migration')->with($data)->render();

        if (!file_exists($migrationFile) && $fs = fopen($migrationFile, 'x')) {
            fwrite($fs, $output);
            fclose($fs);
            return true;
        }

        return false;
    }
}