<?php
declare(strict_types=1);

namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Task\BaseTask;

final class CurlThread
{

    /**
     * @var resource
     */
    private $curlResource;

    /**
     * @var null|BaseTask
     */
    private $task = null;

    public function __construct()
    {
        $this->curlResource = \curl_init();
    }

    /**
     * @param BaseTask $task
     */
    public function setTask(BaseTask $task)
    {
        $this->removeTask();
        $this->task = $task;
    }

    /**
     * @return BaseTask
     */
    public function getTask(): BaseTask
    {
        return $this->task;
    }

    public function removeTask()
    {
        $this->task = null;
        //TODO close and init if in use
        $this->resetResourceOptions();
    }

    /**
     * @return bool
     */
    public function isInUse(): bool
    {
        return $this->task === null ? false : true;
    }

    /**
     * @param resource $curlResource
     * @return bool
     */
    public function isEqualResource($curlResource): bool
    {
        return $this->curlResource === $curlResource;
    }

    private function resetResourceOptions()
    {
        \curl_reset($this->curlResource);
    }

    public function applyCurlOptions()
    {
        \curl_setopt_array($this->curlResource, $this->getTask()->getCurlOptions());
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->curlResource;
    }

    public function getErrorMessage(): string
    {
        return \curl_error($this->getResource());
    }

    public function getErrorCode(): int
    {
        return \curl_errno($this->getResource());
    }

    public function __destruct()
    {
        \curl_close($this->curlResource);
    }
}
