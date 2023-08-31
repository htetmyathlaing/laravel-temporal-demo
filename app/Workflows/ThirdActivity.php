<?php

namespace App\Workflows;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface]
class ThirdActivity
{
    #[ActivityMethod]
    public function handle():string
    {
        return "This is result from third activity";
    }
}
