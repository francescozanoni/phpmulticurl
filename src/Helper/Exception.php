<?php
declare(strict_types=1);

namespace PhpMultiCurl\Helper;

class Exception extends \Exception
{
    public function __construct(string $message)
    {
        return parent::__construct('[PhpMultiCurl] ' . $message);
    }
}
