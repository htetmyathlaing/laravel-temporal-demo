<?php

namespace App\Workflows;

use Carbon\CarbonInterval;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
class TimeoutWorkflow
{
    #[WorkflowMethod]
    public function execute(string $name)
    {
        yield Workflow::timer(CarbonInterval::seconds(3));

        return "Hello $name";
    }
}
