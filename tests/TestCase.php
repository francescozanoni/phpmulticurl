<?php
declare(strict_types=1);

use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\BaseTask;
use PhpMultiCurl\Task\Http as HttpTask;
use PhpMultiCurl\Thread\CurlThreadError;
use Symfony\Component\Process\Process;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Process
     */
    private static $webServerProcess;

    /**
     * @var string
     */
    protected static $host = "localhost";

    /**
     * @var int
     */
    protected static $port = 8080;

    /**
     * @var array arguments of executed onLoad callbacks, indexed by task URL
     */
    protected static $loadCallbackArgs = [];

    /**
     * @var array arguments of executed onError callbacks, indexed by task URL
     */
    protected static $errorCallbackArgs = [];

    public static function setUpBeforeClass(): void
    {
        // https://medium.com/@peter.lafferty/start-phps-built-in-web-server-from-phpunit-9571f38c5045
        self::$webServerProcess = new Process([
            "php",
            "-S",
            self::$host . ":" . self::$port,
            "-t",
            realpath(__DIR__) . "/website"
        ]);
        self::$webServerProcess->start();
        sleep(2);
    }

    public static function tearDownAfterClass(): void
    {
        self::$webServerProcess->stop();
    }

    public function tearDown(): void
    {
        self::$loadCallbackArgs = [];
        self::$errorCallbackArgs = [];
    }

    public static function loadCallback(array $response, HttpTask $task)
    {
        self::$loadCallbackArgs[$task->getUrl()] = [
            "response" => $response,
            "task" => $task
        ];
    }

    public static function errorCallback(CurlThreadError $error, BaseTask $task)
    {
        self::$errorCallbackArgs[$task->getUrl()] = [
            "error" => $error,
            "task" => $task
        ];
    }

    protected function runPhpMultiCurl(array $urls, int $numberOfThreads = 1): void
    {
        $queue = new TasksQueue();

        foreach ($urls as $url) {
            $task = (new HttpTask($url))
                ->setOnLoad("TestCase::loadCallback")
                ->setOnError("TestCase::errorCallback");
            $queue->enqueue($task);
        }

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads($numberOfThreads);
        $phpMultiCurl->executeTasks($queue);
    }

}