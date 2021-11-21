<?php

declare(strict_types=1);

namespace Sports\Betting;

use Sports\Betting\Controllers\FrontController;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}


(new FrontController())->process();
