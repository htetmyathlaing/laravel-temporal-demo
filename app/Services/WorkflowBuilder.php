<?php

namespace App\Services;

use Carbon\CarbonInterval;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowOptions;
use Temporal\Internal\Client\WorkflowProxy;
use Temporal\Workflow;
use Temporal\Workflow\ChildWorkflowOptions;

class WorkflowBuilder
{
    private string $workflowClassName;

    private string $childWorkflowClassName;

    private ?WorkflowOptions $workflowOptions = null;

    private ?ChildWorkflowOptions $childWorkflowOptions = null;

    public function __construct(private readonly WorkflowClient $workflowClient)
    {
    }

    public function setWorkflowClassName(string $workflowClassName): self
    {
        $this->workflowClassName = $workflowClassName;

        return $this;
    }

    public function setWorkflowOptions(?WorkflowOptions $workflowOptions): self
    {
        if (is_null($workflowOptions)) {
            $workflowOptions = WorkflowOptions::new()
                ->withTaskQueue(config('services.temporal.task-queue'))
                ->withWorkflowExecutionTimeout(CarbonInterval::minute(2));
        }
        $this->workflowOptions = $workflowOptions;

        return $this;
    }

    public function setChildWorkflowClassName(string $childWorkflowClassName): self
    {
        $this->childWorkflowClassName = $childWorkflowClassName;

        return $this;
    }

    public function setChildWorkflowOptions(?ChildWorkflowOptions $childWorkflowOptions): self
    {
        if (is_null($childWorkflowOptions)) {
            $childWorkflowOptions = ChildWorkflowOptions::new()
                ->withNamespace(config('services.temporal.namespace'))
                ->withTaskQueue(config('services.temporal.task-queue'))
                ->withWorkflowExecutionTimeout(CarbonInterval::minute(2));
        }

        $this->childWorkflowOptions = $childWorkflowOptions;

        return $this;
    }

    public function getWorkflow(): WorkflowProxy
    {
        return $this->workflowClient->newWorkflowStub(
            $this->workflowClassName,
            $this->workflowOptions
        );
    }

    public function getChildWorkflow(): object
    {
        return Workflow::newChildWorkflowStub($this->childWorkflowClassName, $this->childWorkflowOptions);
    }

    public static function new(): self
    {
        return new self(app(WorkflowClient::class));
    }

    public static function workflow(string $workflowClassName, ?WorkflowOptions $workflowOptions = null): WorkflowProxy
    {
        return self::new()
            ->setWorkflowClassName($workflowClassName)
            ->setWorkflowOptions($workflowOptions)
            ->getWorkflow();
    }

    /**
     * @psalm-template T of object
     *
     * @param  class-string<T>  $childWorkflowClassName
     * @return T
     */
    public static function childWorkflow(string $childWorkflowClassName, ?ChildWorkflowOptions $childWorkflowOptions = null): object
    {
        return self::new()
            ->setChildWorkflowClassName($childWorkflowClassName)
            ->setChildWorkflowOptions($childWorkflowOptions)
            ->getChildWorkflow();
    }
}
