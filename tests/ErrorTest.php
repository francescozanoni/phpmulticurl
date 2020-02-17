<?php
declare(strict_types=1);

use PhpMultiCurl\Task\Http as HttpTask;

class ErrorTest extends TestCase
{

    public function testWrongPort(): void
    {
        $url = self::$host . ":" . (self::$port + 1) . "/index.html";

        $this->runPhpMultiCurl([$url], 1);

        $this->assertEmpty(self::$loadCallbackArgs);
        $this->assertEquals(1, count(self::$errorCallbackArgs));

        $error = self::$errorCallbackArgs[$url]["error"];
        $task = self::$errorCallbackArgs[$url]["task"];
        $host = parse_url($task->getUrl(), PHP_URL_HOST);
        $port = parse_url($task->getUrl(), PHP_URL_PORT) ?? "80";

        $this->assertEquals(
            "Failed to connect to " . $host . " port " . $port . ": Connection refused",
            $error->getMessage()
        );
        $this->assertInstanceOf(HttpTask::class, $task);
    }

    public function testWrongScheme(): void
    {
        $url = "https://" . self::$host . ":" . (self::$port + 1) . "/index.html";

        $this->runPhpMultiCurl([$url], 1);

        $this->assertEmpty(self::$loadCallbackArgs);
        $this->assertEquals(1, count(self::$errorCallbackArgs));

        $error = self::$errorCallbackArgs[$url]["error"];
        $task = self::$errorCallbackArgs[$url]["task"];
        $host = parse_url($task->getUrl(), PHP_URL_HOST);
        $port = parse_url($task->getUrl(), PHP_URL_PORT) ?? "80";

        $this->assertEquals(
            "Failed to connect to " . $host . " port " . $port . ": Connection refused",
            $error->getMessage()
        );
        $this->assertInstanceOf(HttpTask::class, $task);
    }

    public function testHostNotFound(): void
    {
        $url = "q093284q20339kusq09r38qy2092q8y00q92.net";

        $this->runPhpMultiCurl([$url], 1);

        $this->assertEmpty(self::$loadCallbackArgs);
        $this->assertEquals(1, count(self::$errorCallbackArgs));

        $error = self::$errorCallbackArgs[$url]["error"];
        $task = self::$errorCallbackArgs[$url]["task"];

        $this->assertEquals(
            "Could not resolve host: " . $url,
            $error->getMessage()
        );
        $this->assertInstanceOf(HttpTask::class, $task);
    }

}
