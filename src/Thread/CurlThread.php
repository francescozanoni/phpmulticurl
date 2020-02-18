<?php
declare(strict_types=1);

namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Task\BaseTask;

final class CurlThread
{
    private $curlResource = null;
    private $task = null;

    public function __construct()
    {
        $this->curlResource = \curl_init();
    }

    /**
     * @param BaseTask $task e.g. Object (
     *                              [url:protected] => localhost:8081/index.html
     *                              [onLoadCallback:protected] => TestCase::loadCallback
     *                              [onErrorCallback:protected] => TestCase::errorCallback
     *                              [data:protected] =>
     *                              [curlOptions:protected] => Array ()
     *                            )
     */
    public function setTask(BaseTask $task): void
    {
        $this->removeTask();
        $this->task = $task;
    }

    /**
     * @return BaseTask e.g. Object (
     *                         [url:protected] => localhost:8081/index.html
     *                         [onLoadCallback:protected] => TestCase::loadCallback
     *                         [onErrorCallback:protected] => TestCase::errorCallback
     *                         [data:protected] =>
     *                         [curlOptions:protected] => Array ()
     *                       )
     */
    public function getTask(): BaseTask
    {
        return $this->task;
    }

    public function removeTask(): void
    {
        $this->task = null;
        //TODO close and init if in use
        $this->resetResourceOptions();
    }

    public function isInUse(): bool
    {
        return $this->task === null ? false : true;
    }

    /**
     * @param resource $curlResource
     *
     * @return bool
     */
    public function isEqualResource($curlResource): bool
    {
        return $this->curlResource === $curlResource;
    }

    private function resetResourceOptions(): void
    {
        \curl_reset($this->curlResource);
    }

    public function applyCurlOptions(): void
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
