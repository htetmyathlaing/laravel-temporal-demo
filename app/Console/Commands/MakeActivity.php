<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeActivity extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temporal:make-activity  {name : Activity Name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create temporal activity file';

    protected function getStub(): string
    {
        return __DIR__.'/stubs/activity.php.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'/Workflows';
    }

    protected function getNameInput(): string
    {
        return trim($this->argument('name').'Activity');
    }
}
