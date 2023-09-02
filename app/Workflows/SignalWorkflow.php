<?php

namespace App\Workflows;

use Temporal\Workflow;
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
class SignalWorkflow
{
    private array $inputs = [];

    private bool $exit = false;

    #[WorkflowMethod]
    public function execute()
    {
        $result = [];
        while (true) {
            yield Workflow::await(fn () => $this->exit);
            if ($this->exit) {
                foreach ($this->inputs as $name){
                    $result[] = sprintf('Hello, %s!', $name);
                }
                return $result;
            }
        }
    }

    #[SignalMethod]
    public function addName(string $name): void
    {
        $this->inputs[] = $name;
    }

    #[SignalMethod]
    public function exit(): void
    {
        $this->exit = true;
    }

    #[Workflow\QueryMethod]
    public function queryInputs():array
    {
       return $this->inputs;
    }
}
