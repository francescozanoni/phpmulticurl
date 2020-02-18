<?php
declare(strict_types=1);

namespace PhpMultiCurl\Helper;

use PhpMultiCurl\Task\BaseTask;
use SplQueue;

class Queue extends SplQueue
{
    public function enqueue($task)
    {
        // BaseTask cannot be enforced via argument type
        // because it would change method's signature,
        // making it incompatible with parent's one: SplQueue::enqueue($value).
        if (!($task instanceof BaseTask)) {
            throw new \InvalidArgumentException('Queue accepts only BaseTask instance');
        }

        $task->validate();

        parent::enqueue($task);
    }
}
