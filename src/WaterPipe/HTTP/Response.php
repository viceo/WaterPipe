<?php

/**
 * WaterPipe - URL routing framework for PHP
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @category  Library
 * @package   WaterPipe
 * @author    Axel Nana <ax.lnana@outlook.com>
 * @copyright 2018 Aliens Group, Inc.
 * @license   MIT <https://github.com/ElementaryFramework/WaterPipe/blob/master/LICENSE>
 * @version   0.0.1
 * @link      http://waterpipe.na2axl.tk
 */

namespace ElementaryFramework\WaterPipe\HTTP;

use ElementaryFramework\WaterPipe\WaterPipeConfig;

class Response
{
    /**
     * @var ResponseStatus
     */
    private $_status;

    /**
     * @var string
     */
    private $_body;

    /**
     * @var ResponseHeader
     */
    private $_header;

    /**
     * Response constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->_status = new ResponseStatus(ResponseStatus::OkCode);
        $this->_header = new ResponseHeader();
        $this->_body = "";
    }

    /**
     * Sends this response to the client.
     */
    public function send()
    {
        // Set status code
        $code = $this->_status->getCode();
        $text = $this->_status->getDescription();

        if (strpos(PHP_SAPI, 'cgi') === 0) {
            header("Status: {$code} {$text}", true);
        }
        else {
            $protocol = (array_key_exists('SERVER_PROTOCOL', $_SERVER) && NULL !== $_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header("{$protocol} {$code} {$text}", true, $code);
        }

        // Set headers
        foreach ($this->_header as $key => $value) {
            header("{$key}: {$value}", true, $code);
        }

        // Send body
        echo $this->_body;

        // Exit properly
        exit(0);
    }

    /**
     * @param string $body
     * @param int $status
     * @throws \Exception
     */
    public function sendHtml(string $body, int $status = 200)
    {
        $config = WaterPipeConfig::get();

        $this->_status = new ResponseStatus($status);
        $this->_header->setContentType("text/html; charset={$config->getDefaultCharset()}");
        $this->_body = $body;

        $this->send();
    }

    /**
     * @param string $body
     * @param int $status
     * @throws \Exception
     */
    public function sendJsonString(string $body, int $status = 200)
    {
        $config = WaterPipeConfig::get();

        $this->_status = new ResponseStatus($status);
        $this->_header->setContentType("application/json; charset={$config->getDefaultCharset()}");
        $this->_body = $body;

        $this->send();
    }

    /**
     * @param array $json
     * @param int $status
     * @throws \Exception
     */
    public function sendJson(array $json, int $status = 200)
    {
        $this->sendJsonString(json_encode($json), $status);
    }

    /**
     * @param string $body
     * @param int $status
     * @throws \Exception
     */
    public function sendText(string $body, int $status = 200)
    {
        $config = WaterPipeConfig::get();

        $this->_status = new ResponseStatus($status);
        $this->_header->setContentType("text/plain; charset={$config->getDefaultCharset()}");
        $this->_body = $body;

        $this->send();
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->_body = $body;
    }

    /**
     * @param ResponseStatus $status
     */
    public function setStatus(ResponseStatus $status): void
    {
        $this->_status = $status;
    }

    /**
     * @param ResponseHeader $header
     */
    public function setHeader(ResponseHeader $header): void
    {
        $this->_header = $header;
    }
}