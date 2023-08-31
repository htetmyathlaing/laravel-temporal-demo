<?php

namespace App\Workflows;

use App\Workflows\BaseWorkflow;
use Carbon\CarbonInterval;
use InvalidArgumentException;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
class FirstWorkflow extends BaseWorkflow
{
    private $firstActivity, $secondActivity, $thirdActivity, $fourthActivity;

    public function __construct()
    {
        $this->firstActivity = Workflow::newActivityStub(
            FirstActivity::class,
            ActivityOptions::new()->withStartToCloseTimeout(CarbonInterval::seconds(2))
        );

        $this->secondActivity = Workflow::newActivityStub(
            SecondActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::seconds(2))
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(CarbonInterval::seconds(1))
                        ->withMaximumInterval(CarbonInterval::seconds(300))
                        ->withMaximumAttempts(100)
                        ->withNonRetryableExceptions([InvalidArgumentException::class])
                )
        );

        $this->thirdActivity = Workflow::newActivityStub(
            ThirdActivity::class,
            ActivityOptions::new()->withStartToCloseTimeout(CarbonInterval::seconds(2))
        );

        $this->fourthActivity = Workflow::newActivityStub(
            FourthActivity::class,
            ActivityOptions::new()->withStartToCloseTimeout(CarbonInterval::seconds(2))
        );
    }

    #[WorkflowMethod]
    public function execute(string $name)
    {
        $firstResult = yield $this->firstActivity->handle();
        $secondResult = yield $this->secondActivity->handle();

        $thirdResult =  $this->thirdActivity->handle();
        $fourthResult =  $this->fourthActivity->handle();

        $thirdAndFourthResult = (yield $thirdResult) . " - " . (yield $fourthResult);

        return [
            $firstResult,
            $secondResult,
            $thirdAndFourthResult
        ];
    }
}
