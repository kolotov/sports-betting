<?php

declare(strict_types=1);

namespace Sports\Betting\Views;

class FrontView extends AbstractView
{
    private $_content;

    public function __construct()
    {
        $this->_content =  file_get_contents(ABSPATH . 'front/main.html');
    }

    public function render(): void
    {
        echo $this->_content;
    }
}
