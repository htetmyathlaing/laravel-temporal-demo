<?php

namespace App\Workflows;

use Temporal\Exception\IllegalStateException;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowMethod;

#[Workflow\WorkflowInterface]
class RetryWorkflow
{
    #[WorkflowMethod]
    public function retry()
    {
        if (Workflow::getInfo()->attempt < 3) {
            throw new IllegalStateException('not yet');
        }

        return 'Workflow retried '.Workflow::getInfo()->attempt;
    }
}
