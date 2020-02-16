<?php
declare(strict_types=1);

use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\BaseTask;
use PhpMultiCurl\Task\Http as HttpTask;
use PhpMultiCurl\Thread\CurlThreadError;

class MultiThreadTest extends TestCase
{

    public function testFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertEquals(preg_replace("#^http://#", "", $response["url"]), $task->getUrl());
            $this->assertNotEmpty($response["response_content"]);
            $this->assertEquals(200, $response["http_code"]);
            $this->assertEquals(
                strlen($response["response_content"]),
                filesize(__DIR__ . "/website/" . basename($task->getUrl()))
            );
        };

        $urls = [
            self::$host . ":" . self::$port . "/index.html",
            self::$host . ":" . self::$port . "/page1.html",
            self::$host . ":" . self::$port . "/page2.html",
        ];

        $queue = new TasksQueue();

        foreach ($urls as $url) {
            $task = (new HttpTask($url))
                ->setOnLoad($onLoad);
            $queue->enqueue($task);
        }

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(count($urls));
        $phpMultiCurl->executeTasks($queue);
    }

    public function testPathNotFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertEquals(preg_replace("#^http://#", "", $response["url"]), $task->getUrl());
            $this->assertNotEmpty($response["response_content"]);
            $this->assertEquals(404, $response["http_code"]);
        };

        $urls = [
            self::$host . ":" . self::$port . "/abc.html",
            self::$host . ":" . self::$port . "/def.html",
            self::$host . ":" . self::$port . "/ghi.html",
        ];

        $queue = new TasksQueue();

        foreach ($urls as $url) {
            $task = (new HttpTask($url))
                ->setOnLoad($onLoad);
            $queue->enqueue($task);
        }

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(count($urls));
        $phpMultiCurl->executeTasks($queue);
    }

    public function testHostNotFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertEquals(preg_replace("#^http://#", "", $response["url"]), $task->getUrl());
            $this->assertEmpty($response["response_content"]);
            $this->assertEquals(404, $response["http_code"]);
        };

        $onError = function (CurlThreadError $error, BaseTask $task) {
            $this->assertEquals(
                "Failed to connect to " . self::$host . " port " . (self::$port + 1) . ": Connection refused",
                $error->getMessage()
            );
            $this->assertInstanceOf(HttpTask::class, $task);
        };

        $urls = [
            self::$host . ":" . (self::$port + 1) . "/index.html",
            self::$host . ":" . (self::$port + 1) . "/page1.html",
            self::$host . ":" . (self::$port + 1) . "/page2.html",
        ];

        $queue = new TasksQueue();

        foreach ($urls as $url) {
            $task = (new HttpTask($url))
                ->setOnLoad($onLoad)
                ->setOnError($onError);
            $queue->enqueue($task);
        }

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(count($urls));
        $phpMultiCurl->executeTasks($queue);
    }

}
