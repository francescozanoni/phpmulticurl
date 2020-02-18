<?php
declare(strict_types=1);

namespace PhpMultiCurl\Task;

use PhpMultiCurl\Thread\CurlThreadError;

abstract class BaseTask
{
    protected $url = "";
    protected $onLoadCallback = null;
    protected $onErrorCallback = null;
    protected $data = null;
    protected $curlOptions = [];

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setOnLoad(callable $callback): self
    {
        $this->onLoadCallback = $callback;

        return $this;
    }

    public function getOnLoad(): callable
    {
        return $this->onLoadCallback;
    }

    /**
     * @param array $result e.g. Array (
     *                             [url] => http://localhost:8080/index.html
     *                             [content_type] => text/html; charset=UTF-8
     *                             [http_code] => 200
     *                             [header_size] => 121
     *                             [request_size] => 63
     *                             [filetime] => -1
     *                             [ssl_verify_result] => 0
     *                             [redirect_count] => 0
     *                             [total_time] => 0.016
     *                             [namelookup_time] => 1.0E-6
     *                             [connect_time] => 1.0E-6
     *                             [pretransfer_time] => 1.0E-6
     *                             [size_upload] => 0
     *                             [size_download] => 132
     *                             [speed_download] => 8250
     *                             [speed_upload] => 0
     *                             [download_content_length] => 132
     *                             [upload_content_length] => -1
     *                             [starttransfer_time] => 0.016
     *                             [redirect_time] => 0
     *                             [redirect_url] =>
     *                             [primary_ip] => ::1
     *                             [certinfo] => Array()
     *                             [primary_port] => 8080
     *                             [local_ip] => ::1
     *                             [local_port] => 17310
     *                             [request_header] => GET /index.html HTTP/1.1
     *                                                 Host: localhost:8080
     *                                                 Accept: * / *
     *                             [response_header] => HTTP/1.1 200 OK
     *                                                  Host: localhost:8080
     *                                                  Connection: close
     *                                                  Content-Type: text/html; charset=UTF-8
     *                                                  Content-Length: 132
     *                             [response_content] => <!DOCTYPE html>
     *                                                   <html lang="en">
     *                                                   <head>
     *                                                       <meta charset="UTF-8">
     *                                                       <title>Index</title>
     *                                                   </head>
     *                                                   <body>
     *                                                   </body>
     *                                                   </html>
     *                           )
     * @return bool
     */
    public function callOnLoad(array $result): bool
    {
        \call_user_func($this->getOnLoad(), $result, $this);

        return true;
    }

    public function setOnError(callable $callback): self
    {
        $this->onErrorCallback = $callback;

        return $this;
    }

    public function getOnError(): callable
    {
        return $this->onErrorCallback;
    }

    /**
     * @param CurlThreadError $error e.g. Object (
     *                                      [errorCode:PhpMultiCurl\Thread\CurlThreadError:private] => 0
     *                                      [errorString:PhpMultiCurl\Thread\CurlThreadError:private] => Failed to connect to localhost port 8081: Connection refused
     *                                      [task:PhpMultiCurl\Thread\CurlThreadError:private] => PhpMultiCurl\Task\Http Object (
     *                                        [url:protected] => localhost:8081/index.html
     *                                        [onLoadCallback:protected] => TestCase::loadCallback
     *                                        [onErrorCallback:protected] => TestCase::errorCallback
     *                                        [data:protected] =>
     *                                        [curlOptions:protected] => Array()
     *                                      )
     *                                    )
     *
     * @return bool
     */
    public function callOnError(CurlThreadError $error): bool
    {
        \call_user_func($this->getOnError(), $error, $this);

        return true;
    }

    /**
     * @param CurlThreadError $error e.g. Object (
     *                                      [errorCode:PhpMultiCurl\Thread\CurlThreadError:private] => 0
     *                                      [errorString:PhpMultiCurl\Thread\CurlThreadError:private] => Failed to connect to localhost port 8081: Connection refused
     *                                      [task:PhpMultiCurl\Thread\CurlThreadError:private] => PhpMultiCurl\Task\Http Object (
     *                                        [url:protected] => localhost:8081/index.html
     *                                        [onLoadCallback:protected] => TestCase::loadCallback
     *                                        [onErrorCallback:protected] => TestCase::errorCallback
     *                                        [data:protected] =>
     *                                        [curlOptions:protected] => Array()
     *                                      )
     *                                    )
     * @param array $result e.g. Array (
     *                             [url] => http://localhost:8080/index.html
     *                             [content_type] => text/html; charset=UTF-8
     *                             [http_code] => 200
     *                             [header_size] => 121
     *                             [request_size] => 63
     *                             [filetime] => -1
     *                             [ssl_verify_result] => 0
     *                             [redirect_count] => 0
     *                             [total_time] => 0.016
     *                             [namelookup_time] => 1.0E-6
     *                             [connect_time] => 1.0E-6
     *                             [pretransfer_time] => 1.0E-6
     *                             [size_upload] => 0
     *                             [size_download] => 132
     *                             [speed_download] => 8250
     *                             [speed_upload] => 0
     *                             [download_content_length] => 132
     *                             [upload_content_length] => -1
     *                             [starttransfer_time] => 0.016
     *                             [redirect_time] => 0
     *                             [redirect_url] =>
     *                             [primary_ip] => ::1
     *                             [certinfo] => Array()
     *                             [primary_port] => 8080
     *                             [local_ip] => ::1
     *                             [local_port] => 17310
     *                             [request_header] => GET /index.html HTTP/1.1
     *                                                 Host: localhost:8080
     *                                                 Accept: * / *
     *                             [response_header] => HTTP/1.1 200 OK
     *                                                  Host: localhost:8080
     *                                                  Connection: close
     *                                                  Content-Type: text/html; charset=UTF-8
     *                                                  Content-Length: 132
     *                             [response_content] => <!DOCTYPE html>
     *                                                   <html lang="en">
     *                                                   <head>
     *                                                       <meta charset="UTF-8">
     *                                                       <title>Index</title>
     *                                                   </head>
     *                                                   <body>
     *                                                   </body>
     *                                                   </html>
     *
     * @return bool
     */
    public function callCallbacks(?CurlThreadError $error, array $result): bool
    {
        if ($error && $this->getOnError()) {
            return $this->callOnError($error);
        } elseif ($error === null && $this->getOnLoad()) {
            return $this->callOnload($result);
        }

        return false;
    }

    /**
     * Attach data that must be available wherever the task object is used
     *
     * @param mixed $data
     *
     * @return BaseTask
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $options e.g. Array (
     *                              [CURLOPT_HTTPHEADER] => Array (
     *                                                        [0] => Accept-Language: en-us,en;q=0.5
     *                                                        [1] => Accept-Charset: utf-8;q=0.7,*;q=0.7
     *                                                        [2] => Cache-Control: max-age=0
     *                                                        [3] => Pragma:
     *                                                        [4] => Keep-Alive: 300
     *                                                        [5] => Connection: keep-alive
     *                                                      )
     *                              [CURLOPT_REFERER] => http://www.google.com
     *                              [CURLOPT_USERAGENT] => Googlebot/2.1 (+http://www.google.com/bot.html)
     *                              [CURLOPT_FOLLOWLOCATION] => true
     *                              [CURLOPT_AUTOREFERER] => true
     *                              [CURLOPT_MAXREDIRS] => 10
     *                              [CURLOPT_CONNECTTIMEOUT] => 30
     *                              [CURLOPT_TIMEOUT] => 30
     *                              [CURLOPT_DNS_CACHE_TIMEOUT] => 1
     *                              [CURLOPT_SSL_VERIFYHOST] => false
     *                              [CURLOPT_SSL_VERIFYPEER] => false
     *                            )
     *
     * @return BaseTask
     */
    public function setCurlOptions(array $options): self
    {
        $this->curlOptions = $options;

        return $this;
    }

    /**
     * @return array e.g. Array (
     *                      [CURLOPT_HTTPHEADER] => Array (
     *                                                [0] => Accept-Language: en-us,en;q=0.5
     *                                                [1] => Accept-Charset: utf-8;q=0.7,*;q=0.7
     *                                                [2] => Cache-Control: max-age=0
     *                                                [3] => Pragma:
     *                                                [4] => Keep-Alive: 300
     *                                                [5] => Connection: keep-alive
     *                                              )
     *                      [CURLOPT_REFERER] => http://www.google.com
     *                      [CURLOPT_USERAGENT] => Googlebot/2.1 (+http://www.google.com/bot.html)
     *                      [CURLOPT_FOLLOWLOCATION] => true
     *                      [CURLOPT_AUTOREFERER] => true
     *                      [CURLOPT_MAXREDIRS] => 10
     *                      [CURLOPT_CONNECTTIMEOUT] => 30
     *                      [CURLOPT_TIMEOUT] => 30
     *                      [CURLOPT_DNS_CACHE_TIMEOUT] => 1
     *                      [CURLOPT_SSL_VERIFYHOST] => false
     *                      [CURLOPT_SSL_VERIFYPEER] => false
     *                    )
     */
    public function getCurlOptions(): array
    {
        return $this->curlOptions;
    }

    /**
     * Ensure all callbacks have been provided
     *
     * @return bool
     */
    public function validate(): bool
    {
        return $this->onLoadCallback && $this->onErrorCallback;
    }
}
