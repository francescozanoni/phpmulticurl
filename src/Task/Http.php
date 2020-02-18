<?php
declare(strict_types=1);

namespace PhpMultiCurl\Task;

final class Http extends BaseTask
{

    /**
     * @return array e.g. Array (
     *                      [CURLOPT_URL] => http://www.google.com/search
     *                      [CURLOPT_HEADER] => true
     *                      [CURLOPT_RETURNTRANSFER] => true
     *                      [CURLINFO_HEADER_OUT] => true
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
        $options = parent::getCurlOptions();
        $options[\CURLOPT_URL] = $this->url;
        $options[\CURLOPT_HEADER] = true;
        $options[\CURLOPT_RETURNTRANSFER] = true;
        $options[\CURLINFO_HEADER_OUT] = true;

        return $options;
    }

    /**
     * @param array $curlResult e.g. Array (
     *                                 [msg] => 1
     *                                 [result] => 0
     *                                 [handle] => Resource id #123
     *                               )
     *
     * @return bool
     */
    public function callOnLoad(array $curlResult): bool
    {
        $result = \curl_getinfo($curlResult['handle']);
        $content = \curl_multi_getcontent($curlResult['handle']);
        $result['response_header'] = \substr($content, 0, $result['header_size']);
        $result['response_content'] = \substr($content, $result['header_size']);

        return parent::callOnLoad($result);
    }
}
