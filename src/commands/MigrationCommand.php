<?php namespace Invigor\UM;

/**
 * This file is part of UM,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Invigor\UM
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class MigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'um:migration';

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
        $this->laravel->view->addNamespace('um', substr(__DIR__, 0, -8).'views');

        $groupsTable         = Config::get('um.groups_table');
        $groupUserTable      = Config::get('um.group_user_table');
        $rolesTable          = Config::get('um.roles_table');
        $roleUserTable       = Config::get('um.role_user_table');
        $permissionsTable    = Config::get('um.permissions_table');
        $permissionRoleTable = Config::get('um.permission_role_table');

        $this->line('');
        $this->info("Controllers: UserController, GroupController, RoleController, PermissionController");

        $message = "A migration that creates '$groupsTable', '$groupUserTable', '$rolesTable', '$roleUserTable', '$permissionsTable', '$permissionRoleTable'".
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
                    "Couldn't create migration.\n Check the write permissions".
                    " within the database/migrations directory."
                );
            }

            $this->line('');

        }
    }

    /**
     * Create the migration.
     *
     * @param $groupsTable
     * @param $rolesTable
     * @param $roleUserTable
     * @param $permissionsTable
     * @param $permissionRoleTable
     * @return bool
     * @internal param string $name
     *
     */
    protected function createMigration($groupsTable, $groupUserTable, $rolesTable, $roleUserTable, $permissionsTable, $permissionRoleTable)
    {
        $migrationFile = base_path("/database/migrations")."/".date('Y_m_d_His')."_um_setup_tables.php";

        $usersTable  = Config::get('auth.providers.users.table');
        $userModel   = Config::get('auth.providers.users.model');
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
