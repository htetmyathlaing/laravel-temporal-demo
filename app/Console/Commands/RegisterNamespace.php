<?php

namespace App\Console\Commands;

use Google\Protobuf\Duration;
use Illuminate\Console\Command;
use Temporal\Api\Workflowservice\V1\ListNamespacesRequest;
use Temporal\Api\Workflowservice\V1\RegisterNamespaceRequest;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\GRPC\ServiceClientInterface;

class RegisterNamespace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temporal:register-namespace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private ServiceClientInterface $serviceClient;

    public function __construct()
    {
        parent::__construct();

        $this->serviceClient = ServiceClient::create(config('services.temporal.domain'));
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! collect($this->getRegisteredNamespaces())->contains(config('services.temporal.namespace'))) {
            $this->registerNamespace();
            $this->info('Namespace '.config('services.temporal.namespace').' is already registered');

            return self::SUCCESS;
        }
        $this->info('Namespace '.config('services.temporal.namespace').'is registered successfully.');

        return self::SUCCESS;
    }

    private function registerNamespace(): void
    {
        $req = (new RegisterNamespaceRequest())
            ->setNamespace(config('services.temporal.namespace'))
            ->setWorkflowExecutionRetentionPeriod((new Duration())->setSeconds(60 * 60 * 24 * 3));
        $this->serviceClient->RegisterNamespace($req);
    }

    private function getRegisteredNamespaces(): array
    {
        $registeredNamespaces = [];
        foreach ($this->serviceClient->ListNamespaces(new ListNamespacesRequest())->getNamespaces() as $namespace) {
            $registeredNamespaces[] = $namespace->getNamespaceInfo()->getName();
        }

        return $registeredNamespaces;
    }
}
