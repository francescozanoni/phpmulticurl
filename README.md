# PhpMultiCurl

[![Latest Stable Version](https://poser.pugx.org/dypa/phpmulticurl/v/stable.png)](https://packagist.org/packages/dypa/phpmulticurl)
[![License](https://poser.pugx.org/dypa/phpmulticurl/license.png)](https://packagist.org/packages/dypa/phpmulticurl)
[![Total Downloads](https://poser.pugx.org/dypa/phpmulticurl/downloads.png)](https://packagist.org/packages/dypa/phpmulticurl)

Ultra fast non-blocking OOP wrapper for `curl_multi_*` functions.

__Pull requests are very welcome.__

## Main features

* **reuse curl resource**
* don't waste time on unnecessary cycles, careful works with select function
* simple queue management
* fully configured! supports callbacks `onLoad`, `onError`, full control on HTTP headers
* simple usage
* few tests and docs :( sorry :(

## Requirements

* php >= 7.1
* ext-curl
* safe_mode = Off

## Installation

* install [Composer](https://getcomposer.org)
* run `composer require dypa/phpmulticurl`

## Examples

### Basic usage
```php
use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\Http as HttpTask;
use PhpMultiCurl\Task\BaseTask;
use PhpMultiCurl\Thread\CurlThreadError;

$onLoad = function (array $response, HttpTask $task) {
    echo $response['response_content'];
};

$onError = function (CurlThreadError $error, BaseTask $task) {
    echo $error;
};

$queue = new TasksQueue();

$urls = [
    'http://example.com',
    'http://example.org',
    'http://example.net'
];

foreach ($urls as $url) {
    $task = (new HttpTask($url))
        ->setOnLoad($onLoad)
        ->setOnError($onError);
    $queue->enqueue($task);
}

$phpMultiCurl = new PhpMultiCurl();
$phpMultiCurl->setNumberOfThreads(2);
$phpMultiCurl->executeTasks($queue);
```

### More
* [working with options and response](https://github.com/dypa/phpmulticurl/blob/master/examples/example1.php)
* [load URLs in callbacks](https://github.com/dypa/phpmulticurl/blob/master/examples/example2.php)

## Tests

```sh
$ vendor/bin/phpunit tests

# or via Docker
$ docker run --rm \
             -it \
             -v "$(pwd)":/app \
             -w /app \
             php:7.1 \
             /usr/local/bin/php ./vendor/phpunit/phpunit/phpunit tests
$ docker run --rm \
             -it \
             -v "$(pwd)":/app \
             -w /app \
             php:7.2 \
             /usr/local/bin/php ./vendor/phpunit/phpunit/phpunit tests
$ docker run --rm \
             -it \
             -v "$(pwd)":/app \
             -w /app \
             php:7.3 \
             /usr/local/bin/php ./vendor/phpunit/phpunit/phpunit tests
$ docker run --rm \
             -it \
             -v "$(pwd)":/app \
             -w /app \
             php:7.4 \
             /usr/local/bin/php ./vendor/phpunit/phpunit/phpunit tests
```

## Contributing

Fork the project, create a feature branch and send us a pull request.

To ensure a consistent code base, you should make sure the code follows
the [PSR-*](http://www.php-fig.org/psr) coding standards.

To avoid CS issues, you should use [php-cs-fixer](http://cs.sensiolabs.org):

```sh
$ php-cs-fixer fix src/
```
