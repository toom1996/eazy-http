<?php

namespace eazy\http;

use eazy\http\base\BaseComponent;
use eazy\http\exceptions\HttpException;
use Swoole\Coroutine;

/**
 * @property \Swoole\Http\Response $response
 * @property integer $statusCode
 * @property string $content
 * @property string $stream
 * @property array $headers
 * @property bool $isSend
 * @property \Swoole\Http\Response $context
 */
class Response extends Component
{
    /**
     * Default response status code.
     * @var int
     */
    public $defaultStatusCode = 200;

    /**
     * List of HTTP status codes and the corresponding texts.
     * @var string[]
     */
    public static $httpStatuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    public $format = self::FORMAT_HTML;

    const FORMAT_HTML = 'html';

    public function send()
    {
        if ($this->isSend) {
            return false;
        }
        $this->prepare();
        $this->sendHeaders();
        $this->sendContent();
    }

    public function sendContent()
    {
        // Set isSend is true.
        // Prevent duplicate output.
        $this->setAttribute('isSend', true);
        if (!$this->content) {
            $this->content = ob_get_clean();
        }
        var_dump($this->response);
        $this->response->setStatusCode($this->statusCode);
        $this->response->end($this->content);
    }
    
    public function prepare()
    {
        if ($this->statusCode === 204) {
            $this->content = '';
            $this->stream = null;
            return;
        }

        if ($this->stream !== null) {
            return;
        }
    }


    /**
     * Set send header.
     */
    public function sendHeaders()
    {
        foreach ($this->headers as $name => $value) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            // set replace for first occurrence of header but false afterwards to allow multiple
            $this->response->header($name, $value);
        }
        //        $this->sendCookies();
    }
    
    public function getIsSend()
    {
        return $this->attributes['isSend'] ?? false;
    }

    public function getStatusCode()
    {
        return $this->attributes['statusCode'] ?? $this->defaultStatusCode;
    }

    public function setStatusCode($code)
    {
        $this->setAttribute('statusCode', $code);
        return $this;
    }

    public function getContent()
    {
        return $this->attributes['content'] ?? null;
    }

    public function setContent($content)
    {
        $this->setAttribute('content', $content);
        return $this;
    }

    public function getStream()
    {
        return $this->attributes['stream'] ?? null;
    }

    public function setResponse(\Swoole\Http\Response $response)
    {
        $this->setAttribute('response', $response);
    }

    public function getResponse()
    {
        return $this->attributes['response'];
    }

    public function setStream($stream)
    {
        $this->setAttribute('stream', $stream);
        return $this;
    }

    public function getHeaders()
    {
        return $this->attributes['headers'] ?? [];
    }

    public function setHeaders($headers = [], $append = false)
    {
        if ($append) {
            $headers = array_merge($this->headers, $headers);
        }
        $this->setAttribute('headers', $headers);

        return $this;
    }
    
    public function setStatusCodeByException($exception)
    {
        if ($exception instanceof HttpException) {
            $this->setStatusCode($exception->getCode());
        } else {
            $this->setStatusCode(500);
        }

        return $this;
    }
}