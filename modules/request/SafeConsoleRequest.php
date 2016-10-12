<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 12.10.2016
 * Time: 14:10
 */

namespace stradivari\request;

class SafeConsoleRequest extends ASafeRequest {
    public function __construct(ConsoleRequest $request) {
        parent::__construct($request);
    }
}
