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
            yield Workflow::await(fn () => $this->inputs !== [] || $this->exit);
            if ($this->inputs === [] && $this->exit) {
                return $result;
            }

            $name = array_shift($this->input);
            $result[] = sprintf('Hello, %s!', $name);
        }
    }

    #[SignalMethod]
    public function addName(string $name): void
    {
        $this->input[] = $name;
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
