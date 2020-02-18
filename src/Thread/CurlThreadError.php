<?php
declare(strict_types=1);

namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Task\BaseTask;

final class CurlThreadError
{
    private $errorCode = 0;
    private $errorString = '';
    private $task;

    /**
     * CurlThreadError constructor.
     *
     * @param int $errorCode e.g. 0
     * @param string $errorString e.g. Failed to connect to localhost port 8081: Connection refused
     * @param BaseTask $task e.g. Object (
     *                              [url:protected] => localhost:8081/index.html
     *                              [onLoadCallback:protected] => TestCase::loadCallback
     *                              [onErrorCallback:protected] => TestCase::errorCallback
     *                              [data:protected] =>
     *                              [curlOptions:protected] => Array()
     *                            )
     */
    public function __construct(int $errorCode, string $errorString, BaseTask $task)
    {
        $this->errorCode = $errorCode;
        $this->errorString = $errorString;
        $this->task = $task;
    }

    /**
     * @return int e.g. 0
     */
    public function getCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return string e.g. Failed to connect to localhost port 8081: Connection refused
     */
    public function getMessage(): string
    {
        return $this->errorString;
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

    /**
     * @return string e.g. Failed to connect to localhost port 8081: Connection refused
     */
    public function __toString(): string
    {
        return $this->getMessage();
    }
}
