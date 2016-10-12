<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 07.10.2016
 * Time: 22:21
 */

namespace stradivari\response;

use \stradivari\request\ConsoleRequest;

class ConsoleResponse {
    public $body;
    public $response;
    public $outputStream;

    public function __construct() {
        $this->outputStream = fopen('php://stdout', 'w');
    }

    public function send() {
        fprintf($this->outputStream, "%s", $this->body);
    }
}
