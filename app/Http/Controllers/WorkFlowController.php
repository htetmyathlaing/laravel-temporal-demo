<?php

namespace App\Http\Controllers;

use App\Services\WorkflowBuilder;
use App\Workflows\AsyncWorkflow;
use App\Workflows\FirstWorkflow;
use App\Workflows\RetryWorkflow;
use App\Workflows\SignalWorkflow;
use App\Workflows\TimeoutWorkflow;
use Carbon\CarbonInterval;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowOptions;
use Temporal\Common\RetryOptions;

class WorkFlowController extends Controller
{
    public function __construct(private readonly WorkflowClient $workflowClient)
    {
    }

    public function startFirstWorkflow(): JsonResponse
    {
        $workflow = WorkflowBuilder::workflow(FirstWorkflow::class);

        $run = $this->workflowClient->start($workflow, "John");

        $workflowID = $run->getExecution()->getID();

        return Response::json([
            'workflow_id' => $workflowID,
//            'result' => $run->getResult(),
        ]);
    }

    public function startAsyncWorkflow(): JsonResponse
    {
        $workflow = WorkflowBuilder::workflow(AsyncWorkflow::class);

        $run = $this->workflowClient->start($workflow);

        $workflowID = $run->getExecution()->getID();

        return Response::json([
            'workflow_id' => $workflowID,
        ]);
    }

    public function startRetryWorkflow(): JsonResponse
    {
        $workflowOptions = WorkflowOptions::new()
            ->withTaskQueue(config('services.temporal.task-queue'))
            ->withRetryOptions(
                RetryOptions::new()
                    ->withInitialInterval(CarbonInterval::seconds(1))
                    ->withMaximumInterval(CarbonInterval::seconds(100))
                    ->withMaximumAttempts(3)
                    ->withNonRetryableExceptions([\InvalidArgumentException::class])
            );
        $workflow = WorkflowBuilder::workflow(RetryWorkflow::class, $workflowOptions);

        $run = $this->workflowClient->start($workflow);

        $workflowID = $run->getExecution()->getID();

        return Response::json([
            'workflow_id' => $workflowID,
            'result' => $run->getResult(),
        ]);
    }

    public function startSignalWorkflow(): JsonResponse
    {
        /** @var SignalWorkflow $workflow */
        $workflow = WorkflowBuilder::workflow(SignalWorkflow::class);

        $run = $this->workflowClient->start($workflow);

        $workflowID = $run->getExecution()->getID();

        $workflow->addName('Name 1');
        $workflow->addName('Name 2');
        $query1 = $workflow->queryInputs();

        $workflow->addName('Name 3');
        $workflow->exit();

        return Response::json([
            'workflow_id' => $workflowID,
            'query1' => $query1,
            'result' => $run->getResult(),
        ]);
    }

    public function startTimeoutWorkflow(): JsonResponse
    {
        $workflowOptions = WorkflowOptions::new()
            ->withTaskQueue(config('services.temporal.task-queue'))
            ->withWorkflowExecutionTimeout(CarbonInterval::seconds(2));
        $workflow = WorkflowBuilder::workflow(TimeoutWorkflow::class, $workflowOptions);

        $run = $this->workflowClient->start($workflow, 'Jon');

        $workflowID = $run->getExecution()->getID();

        return Response::json([
            'workflow_id' => $workflowID,
            'result' => $run->getResult(),
        ]);
    }
}
