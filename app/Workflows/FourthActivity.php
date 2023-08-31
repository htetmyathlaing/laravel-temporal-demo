<?php

namespace App\Workflows;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface]
class FourthActivity
{
    #[ActivityMethod]
    public function handle():string
    {
        return "This is result from fourth activity";
    }
}
