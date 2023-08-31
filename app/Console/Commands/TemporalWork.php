<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;
use Temporal\WorkerFactory;

class TemporalWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temporal:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start processing temporal workflows as a daemon';

    private ClassLocator $classLocator;

    public function __construct()
    {
        parent::__construct();

        $this->classLocator = new ClassLocator(
            Finder::create()->files()->in(app_path('Workflows'))
        );
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $factory = WorkerFactory::create();
        $worker = $factory->newWorker(config('services.temporal.task-queue'));

        foreach ($this->getWorkflowTypes() as $workflowType) {
            $worker->registerWorkflowTypes($workflowType);
        }

        foreach ($this->getActivityTypes() as $activityType) {
            $worker->registerActivity($activityType);
        }

        $factory->run();

        return 0;
    }

    private function getActivityTypes(): \Generator
    {
        foreach ($this->getAvailableDeclarations() as $class) {
            if (Str::endsWith($class->getName(), 'Activity')) {
                yield $class->getName();
            }
        }
    }

    private function getWorkflowTypes(): \Generator
    {
        foreach ($this->getAvailableDeclarations() as $class) {
            if (Str::endsWith($class->getName(), 'Workflow')) {
                yield $class->getName();
            }
        }
    }

    private function getAvailableDeclarations(): \Generator
    {
        foreach ($this->classLocator->getClasses() as $class) {
            if ($class->isAbstract() || $class->isInterface()) {
                continue;
            }

            yield $class;
        }
    }
}
