<?php

namespace AndreasPabst\LaravelMakeCompleteModelSet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeCompleteModelSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:complete-model-set {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create API Controller, Resource, Model and Migration';

    private $model = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getStub($type)
    {
        if ($type == "Model") {

        } elseif ($type == "Controller") {
            return file_get_contents(__DIR__."/../../resources/stubs/controller.stub");
        } elseif ($type == "Resource") {
            return file_get_contents(__DIR__."/../../resources/stubs/resource.stub");
        } elseif ($type == "ResourceCollection") {
            return file_get_contents(__DIR__."/../../resources/stubs/resource.collection.stub");
        } elseif ($type == "Request") {
            return file_get_contents(__DIR__."/../../resources/stubs/request.stub");
        }
    }

    protected function controller()
    {
        $controllerTemplate = str_replace(
            ['DummyModelStrToLower', 'DummyModel', 'DummyClass', 'DummyNamespace', 'DummyRootNamespace'],
            [strtolower($this->model), $this->model, "{$this->model}Controller", app()->getNamespace()."Http\\Controllers", app()->getNamespace()],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("Http/Controllers/{$this->model}Controller.php"), $controllerTemplate);
    }

    protected function resource()
    {
        $resourceTemplate = str_replace(
            ['DummyClass', 'DummyNamespace'],
            [$this->model, app()->getNamespace()."Http\\Resources"],
            $this->getStub('Resource')
        );

        file_put_contents(app_path("Http/Resources/{$this->model}.php"), $resourceTemplate);
    }

    protected function resourceCollection()
    {
        $resourceTemplate = str_replace(
            ['DummyClass', 'DummyNamespace'],
            ["{$this->model}Collection", app()->getNamespace()."Http\\Resources"],
            $this->getStub('ResourceCollection')
        );

        file_put_contents(app_path("Http/Resources/{$this->model}Collection.php"), $resourceTemplate);
    }

    protected function request()
    {
        $types = array("Show", "Index", "Update", "Store", "Destroy");
        mkdir(app_path("Http/Requests/{$this->model}"));
        foreach ($types as $type) {
            $requestTemplate = str_replace(
                ['DummyClass', 'DummyNamespace', 'DummyRootNamespace'],
                ["{$this->model}{$type}Request", app()->getNamespace()."Http\\Requests\\".$this->model, app()->getNamespace()],
                $this->getStub('Request')
            );

            file_put_contents(app_path("Http/Requests/{$this->model}/{$this->model}{$type}Request.php"), $requestTemplate);
        }
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // First Welcome
        $this->info("Generating complete controller set");
        while(empty($this->model)) {
            if (!$this->argument('name')) {
                $this->model = ucfirst($this->ask("Enter the Model Name... <fg=white>(You can pass the name as a parameter as well)</>"));
            } else {
                $this->model = ucfirst($this->argument('name'));
            }

        }

        // give a user feedback that Model already exists
        if (file_exists(app_path()."/".$this->model.".php")) {
            $this->error('Model already exists...'); exit;
        }

        // Ask for model file creation
        if ($this->confirm("<fg=white>1.</> Do you want to create the model <fg=yellow>`{$this->model}`</>?")) {
            $this->info("  Crafting model...");
            Artisan::call('make:model', ['name' => $this->model]);
            if ($this->confirm("   Do you want a migration?")) {
                // MakeThisONE to make_this_one
                $migname = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $this->model)), '_');
                $migname = "create_".$migname."s_table";

                $this->info("  Crafting migration `<fg=yellow>{$migname}</>`...");
                Artisan::call('make:migration', ['name' => $migname]);
            }
        }

        // ask for resource creation
        if ($this->confirm("<fg=white>2.</> Do you want Resources <fg=yellow>`{$this->model}, {$this->model}Collection`</>?")) {
            $this->info("  Crafting resource <fg=yellow>`{$this->model}`</>...");
            $this->resource($this->model);
            $this->info("  Crafting collection <fg=yellow>`{$this->model}Collection`</>...");
            $this->resourceCollection($this->model);
        }

        // ask for controller creation
        if ($this->confirm("<fg=white>2.</> Do you want a controller <fg=yellow>`{$this->model}Controller`</>?")) {
            $controllerType = $this->choice('Which controller type do you want?', ['normal', 'apiResource', 'webResource'], 0);

            if ($this->confirm("Do you want to including all requests into controller creation?")) {
                $this->info("  Crafting request...");
                $this->request($this->model);
                $this->info("  Crafting controller...");
                $this->controller($this->model);
            } else {
                $this->info("  Crafting controller only...");
                Artisan::call('make:controller', ['name' => "{$this->model}Controller"]);
            }
        }
    }
}
