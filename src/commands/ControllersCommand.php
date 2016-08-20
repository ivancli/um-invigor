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
    protected $signature = 'um:controller {--entity=}';
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
    protected $description = 'Creates a controllers for UM entities.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->laravel->view->addNamespace('um', substr(__DIR__, 0, -8) . 'views');

        $userController = Config::get('um.user_controller');
        $groupController = Config::get('um.group_controller');
        $roleController = Config::get('um.role_controller');
        $permissionController = Config::get('um.permission_controller');

        $this->line('');
        $this->info("Controllers: $userController, $groupController, $roleController, $permissionController");

        if (isset($this->option()['entity'])) {
            switch ($this->option('entity')) {
                case "user":
                    $message = "'$userController' will be created in app/Http/Controller/um directory";
                    break;
                case "group":
                    $message = "'$groupController' will be created in app/Http/Controller/um directory";
                    break;
                case "role":
                    $message = "'$roleController' will be created in app/Http/Controller/um directory";
                    break;
                case "permission":
                    $message = "'$permissionController' will be created in app/Http/Controller/um directory";
                    break;
                default:
            }
        } else {
            $message = "'$userController', '$groupController', '$roleController', '$permissionController'" .
                " will be created in app/Http/Controller/um directory";
        }
        $this->comment($message);
        $this->line('');

        if ($this->confirm("Proceed with the controllers creation? [Yes|no]", "Yes")) {

            $this->line('');

            $this->info("Creating controllers...");
            if (isset($this->option()['entity'])) {
                switch ($this->option('entity')) {
                    case "user":
                        $this->createOneController($userController);
                        break;
                    case "group":
                        $this->createOneController($groupController);
                        break;
                    case "role":
                        $this->createOneController($roleController);
                        break;
                    case "permission":
                        $this->createOneController($permissionController);
                        break;
                    default:
                }
            } else {
                /* Create all controllers */
                $this->createOneController($userController);
                $this->createOneController($groupController);
                $this->createOneController($roleController);
                $this->createOneController($permissionController);
            }
            $this->line('');
        }
    }

    protected function createOneController($controllerName)
    {
        /* Create user controller */
        if ($this->createController($controllerName)) {
            $this->info("$controllerName successfully created!");
        } else {
            $this->error(
                "Couldn't create $controllerName.\n Check the write permissions" .
                " within the app/Http/Controller/um directory."
            );
        }
    }

    /**
     * Create Controller
     * @param $className
     * @return bool
     */
    protected function createController($className)
    {
        $folderPath = app_path("Http/Controllers/UM");
        $controllerFile = $folderPath . '/' . $className . ".php";

        $data = compact(['className']);

        $output = $this->laravel->view->make('um::generators.controllers.extend_controller')->with($data)->render();
        if (!is_dir($folderPath)) {
            mkdir($folderPath);
        }
        if (!file_exists($controllerFile) && $fs = fopen($controllerFile, 'x')) {
            fwrite($fs, $output);
            fclose($fs);
            return true;
        }

        return false;
    }
}