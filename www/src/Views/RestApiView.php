<?php

declare(strict_types=1);

namespace Sports\Betting\Views;

class RestApiView extends AbstractView
{
    private $_json;
    private $_code;
    private $_location;

    public function __construct()
    {
    }

    public function setOutput(array $data, $location = ''): void
    {
        $this->_code = $data['success']['code'] ?? ($data['error']['code'] ?? null);
        $this->_json = json_encode($data);
        $this->_location = $location;
    }

    private function _getHeaderTextByCode(int $code): string
    {
        $codes = [
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            412 => 'Precondition Failed',
            415 => 'Unsupported Media Type',
            500 => 'Internal Server Error',
            501 => 'Not Implemented'
        ];

        if (isset($codes[$code]) === false) {
            //throw new Exception('Response code not supported');
            return '';
        }

        return $codes[$code];
    }

    private function _sendHeaders(): void
    {
        header('access-Control-Allow-Origin: *');
        header("access-Control-Allow-Methods: GET, PUT");
        header("Access-Control-Max-Age: 3600");
        header("Content-Type: application/json; charset=UTF-8");

        if (!empty($this->_location)) {
//            header("Location: {$this->_location}");
        }

        $code = $this->_code;

        //response header
        if (is_numeric($this->_code)) {
            $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';

            $text = $this->_getHeaderTextByCode($this->_code);
            if (empty($text)) {
                $code = 500;
                $text = $this->_getHeaderTextByCode($code);
            }

            header("{$protocol} {$code} {$text}");
        }
    }

    public function render(): void
    {
        $this->_sendHeaders();
        print($this->_json);
    }
}
