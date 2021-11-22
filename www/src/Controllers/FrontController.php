<?php

declare(strict_types=1);

namespace Sports\Betting\Controllers;

class FrontController extends AbstractController
{
    public function __construct()
    {
        $this->_view = $this->loadView('FrontView');
    }

    public function process(): void
    {
        $request =  $_SERVER['REQUEST_URI'] ?? '';
        [$prepare_entry] = array_slice(explode('/', $request), 1, 1);
        $entry = preg_replace('/[^a-z0-9.\/]/s', '', mb_strtolower($prepare_entry));


        switch ($entry) {
        case 'api':
            $api = new RestApiController();
            $api->process('/api/');
            break;

        case '':
        case 'index.php':
        case 'index.html':
            $this->_render();
            break;

        case 'css':
        case 'js':
        case 'bower_components':
            break;

        default:
            $this->_pageNotFound();
            break;
        }
    }

    private function _render(): void
    {
        $this->_view->render();
    }

    private function _pageNotFound(): void
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';
        header("{$protocol} 404 Not Found");
        echo "Page Not Found";
    }
}
