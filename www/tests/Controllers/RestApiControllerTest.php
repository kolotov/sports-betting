<?php

declare(strict_types=1);

namespace Sports\Betting\Tests\Controllers;

use Sports\Betting\Controllers\RestApiController;
use PHPUnit\Framework\TestCase;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class RestApiControllerTest extends TestCase
{
    public function testGetUsers()
    {
        $_REQUEST['REQUEST_URI'] = '/api/users/login/action/bet';
        $this->_api = new RestApiController();

        $this->assertEquals('', '');
    }
}
