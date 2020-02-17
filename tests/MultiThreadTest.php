<?php
declare(strict_types=1);

class MultiThreadTest extends TestCase
{

    public function testFound(): void
    {
        $hostAndPort = self::$host . ":" . self::$port;
        $urls = [
            $hostAndPort . "/index.html",
            $hostAndPort . "/page1.html",
            $hostAndPort . "/page2.html",
        ];

        $this->runPhpMultiCurl($urls, count($urls));

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

    /**
     * Host/port found and path not found do not trigger error callback
     */
    public function testPathNotFound(): void
    {
        $hostAndPort = self::$host . ":" . self::$port;
        $urls = [
            $hostAndPort . "/abc.html",
            $hostAndPort . "/def.html",
            $hostAndPort . "/ghi.html",
        ];

        $this->runPhpMultiCurl($urls, count($urls));

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

}
