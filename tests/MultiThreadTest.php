<?php
declare(strict_types=1);

use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\BaseTask;
use PhpMultiCurl\Task\Http as HttpTask;
use PhpMultiCurl\Thread\CurlThreadError;

class MultiThreadTest extends TestCase
{

    private function runPhpMultiCurl(array $urls): void
    {
        $queue = new TasksQueue();
        
        foreach ($urls as $url) {
            $task = (new HttpTask($url))
                ->setOnLoad("TestCase::loadCallback")
                ->setOnError("TestCase::errorCallback");
            $queue->enqueue($task);
        }

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(count($urls));
        $phpMultiCurl->executeTasks($queue);
    }
    
    public function testFound(): void
    {
        $urls = [
            self::$host . ":" . self::$port . "/index.html",
            self::$host . ":" . self::$port . "/page1.html",
            self::$host . ":" . self::$port . "/page2.html",
        ];

        $this->runPhpMultiCurl($urls);
        
        $this->assertEquals(count($urls), count(self::$loadCallbackArgs));
        $this->assertEmpty(self::$errorCallbackArgs);
        foreach ($urls as $url) {
            $response = self::$loadCallbackArgs[$url]["response"];
            $task = self::$loadCallbackArgs[$url]["task"];
            $this->assertEquals($response["url"], "http://" . $task->getUrl());
            $this->assertNotEmpty($response["response_content"]);
            $this->assertEquals(200, $response["http_code"]);
            $this->assertEquals(
                strlen($response["response_content"]),
                filesize(__DIR__ . "/website/" . basename($task->getUrl()))
            );
        }
    }

    public function testPathNotFound(): void
    {
        $urls = [
            self::$host . ":" . self::$port . "/abc.html",
            self::$host . ":" . self::$port . "/def.html",
            self::$host . ":" . self::$port . "/ghi.html",
        ];

        $this->runPhpMultiCurl($urls);
        
        $this->assertEquals(count($urls), count(self::$loadCallbackArgs));
        $this->assertEmpty(self::$errorCallbackArgs);
        foreach ($urls as $url) {
            $response = self::$loadCallbackArgs[$url]["response"];
            $task = self::$loadCallbackArgs[$url]["task"];
            $this->assertEquals($response["url"], "http://" . $task->getUrl());
            $this->assertNotEmpty($response["response_content"]);
            $this->assertEquals(404, $response["http_code"]);
        }
    }

    public function testHostNotFound(): void
    {
        $urls = [
            self::$host . ":" . (self::$port + 1) . "/index.html",
            self::$host . ":" . (self::$port + 1) . "/page1.html",
            self::$host . ":" . (self::$port + 1) . "/page2.html",
        ];

        $this->runPhpMultiCurl($urls);
        
        $this->assertEmpty(self::$loadCallbackArgs);
        $this->assertEquals(count($urls), count(self::$errorCallbackArgs));
        foreach ($urls as $url) {
            $error = self::$errorCallbackArgs[$url]["error"];
            $task = self::$errorCallbackArgs[$url]["task"];
            $this->assertEquals(
                "Failed to connect to " . self::$host . " port " . (self::$port + 1) . ": Connection refused",
                $error->getMessage()
            );
            $this->assertInstanceOf(HttpTask::class, $task);
        }
    }

}
