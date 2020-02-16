<?php
declare(strict_types=1);

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

}