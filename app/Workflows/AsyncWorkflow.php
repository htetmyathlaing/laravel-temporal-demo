<?php

namespace App\Workflows;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
class AsyncWorkflow
{
    #[WorkflowMethod]
    public function execute()
    {
        $first = Carbon::now()->timestamp;
        yield Workflow::timer(CarbonInterval::seconds(2));
        $second = Carbon::now()->timestamp;

        $third = Workflow::async(function () {
            yield Workflow::timer(CarbonInterval::seconds(2));

            return Carbon::now()->timestamp;
        });

        $fourth = Workflow::async(function () {
            yield Workflow::timer(CarbonInterval::seconds(2));

            return Carbon::now()->timestamp;
        });

        return [
            $first,
            yield $third,
            yield $third
        ];
    }
}
