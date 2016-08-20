<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 20/08/2016
 * Time: 9:14 PM
 */

namespace Invigor\UM;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SeederCommand extends Command
{
    protected $signature = 'um:seeder';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'um:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeding data for User Management Module';

    protected $fileClassMapping = array(
        'InitialSeeder' => 'init_seeder',
    );

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->laravel->view->addNamespace('um', substr(__DIR__, 0, -8) . 'views');

        $this->line('Copying seeder across.');
        if ($this->createSeeder('InitialSeeder')) {
            $this->line("");
            $this->line("InitialSeeder file has been copied to database/seeds folder.");
            $this->warn("Now running composer dump-autoload.");
            shell_exec("composer dump-autoload");
            if ($this->confirm("Proceed seeding admin initial data? [Yes|no]", "Yes")) {
                shell_exec("php artisan db:seed --class=InitialSeeder");
                $this->line("Admin data has been seeded to DB");
            } else {
                $this->line("Please run \"php artisan db:seed --class=InitialSeeder\" manually to seed initial data.");
            }
        }
    }

    public function createSeeder($className)
    {
        $folderPath = base_path("database/seeds");
        $seederFile = $folderPath . '/' . $className . ".php";
        $usersTable = Config::get('auth.providers.users.table');
        if (is_null($usersTable) || empty($usersTable) || $usersTable == '') {
            $usersTable = Config::get('um.users_table');
        }
        $rolesTable = Config::get('um.roles_table');
        $roleUserTable = Config::get('um.role_user_table');
        $data = compact(['usersTable', 'rolesTable', 'roleUserTable']);
        $output = $this->laravel->view->make('um::generators.seeders.' . $this->fileClassMapping[$className])->with($data)->render();
        if (!file_exists($seederFile) && $fs = fopen($seederFile, 'x')) {
            fwrite($fs, $output);
            fclose($fs);
            return true;
        }
        return true;
    }
}