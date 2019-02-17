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
    protected $signature = 'make:complete-model-set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create API Controller, Resource, Model and Migration';

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
            return file_get_contents(resource_path("stubs/completemodelset/controller.stub"));
        } elseif ($type == "Resource") {
            return file_get_contents(resource_path("stubs/completemodelset/resource.stub"));
        } elseif ($type == "ResourceCollection") {
            return file_get_contents(resource_path("stubs/completemodelset/resource.collection.stub"));
        } elseif ($type == "Request") {
            return file_get_contents(resource_path("stubs/completemodelset/request.stub"));
        }
    }

    protected function controller($model)
    {
        $controllerTemplate = str_replace(
            ['DummyModelStrToLower', 'DummyModel', 'DummyClass', 'DummyNamespace', 'DummyRootNamespace'],
            [strtolower($model), $model, "{$model}Controller", app()->getNamespace()."Http\\Controllers", app()->getNamespace()],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("Http/Controllers/{$model}Controller.php"), $controllerTemplate);
    }

    protected function resource($model)
    {
        $resourceTemplate = str_replace(
            ['DummyClass', 'DummyNamespace'],
            [$model, app()->getNamespace()."Http\\Resources"],
            $this->getStub('Resource')
        );

        file_put_contents(app_path("Http/Resources/{$model}.php"), $resourceTemplate);
    }

    protected function resourceCollection($model)
    {
        $resourceTemplate = str_replace(
            ['DummyClass', 'DummyNamespace'],
            ["{$model}Collection", app()->getNamespace()."Http\\Resources"],
            $this->getStub('ResourceCollection')
        );

        file_put_contents(app_path("Http/Resources/{$model}Collection.php"), $resourceTemplate);
    }

    protected function request($model)
    {
        $types = array("Show", "Index", "Update", "Store", "Destroy");
        mkdir(app_path("Http/Requests/{$model}"));
        foreach ($types as $type) {
            $requestTemplate = str_replace(
                ['DummyClass', 'DummyNamespace', 'DummyRootNamespace'],
                ["{$model}{$type}Request", app()->getNamespace()."Http\\Requests\\".$model, app()->getNamespace()],
                $this->getStub('Request')
            );

            file_put_contents(app_path("Http/Requests/{$model}/{$model}{$type}Request.php"), $requestTemplate);
        }
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Generating complete controller set");
        $model = $this->ask("Enter the Model Name...");

        if ($this->confirm("Do you want a Model?")) {
            $this->info("  Crafting model...");
            Artisan::call('make:model', ['name' => $model]);
            if ($this->confirm("Do you want a migration?")) {
                $this->info("  Crafting migration...");
                // MakeThisONE to make_this_one
                $migname = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $model)), '_');
                Artisan::call('make:migration', ['name' => "create_".$migname."s_table"]);
            }
        }


        if ($this->confirm("Do you want Resources?")) {
            $this->info("  Crafting resource...");
            $this->resource($model);
            $this->info("  Crafting collection...");
            $this->resourceCollection($model);
        }

        if ($this->confirm("Do you want a controller?")) {
            if ($this->confirm("Do you want to including all requests into controller creation?")) {
                $this->info("  Crafting request...");
                $this->request($model);
                $this->info("  Crafting controller...");
                $this->controller($model);
            } else {
                $this->info("  Crafting controller only...");
                Artisan::call('make:controller', ['name' => "{$model}Controller"]);
            }
        }
    }
}
