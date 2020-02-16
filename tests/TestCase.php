<?php
declare(strict_types=1);

use Symfony\Component\Process\Process;
use PhpMultiCurl\Task\BaseTask;
use PhpMultiCurl\Task\Http as HttpTask;
use PhpMultiCurl\Thread\CurlThreadError;

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
     * @var array
     */
    protected static $loadCallbackArgs = [];
    
    /**
     * @var array
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

}