<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class MakeWorkflow extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temporal:make-workflow  {name : Workflow Name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create temporal workflow file';

    protected function getStub(): string
    {
        return __DIR__.'/stubs/workflow.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'/Workflows';
    }

    protected function getNameInput(): string
    {
        return trim($this->argument('name').'Workflow');
    }

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $this->createBaseWorkflow();

        parent::handle();
    }

    /**
     * @throws FileNotFoundException
     */
    public function createBaseWorkflow(): void
    {
        $fileName = 'BaseWorkflow';
        if (! $this->alreadyExists($fileName)) {
            $name = $this->qualifyClass($fileName);
            $stub = $this->files->get(__DIR__.'/stubs/base-workflow.php.stub');
            $path = $this->getPath($name);
            $content = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

            $this->makeDirectory($path);

            $this->files->put($path, $this->sortImports($content));
        }
    }
}
