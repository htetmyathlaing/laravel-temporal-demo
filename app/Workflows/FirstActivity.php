<?php

namespace App\Workflows;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface]
class FirstActivity
{
    #[ActivityMethod]
    public function handle():string
    {
        return "This is result from first activity";
    }
}
