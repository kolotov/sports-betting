<?php


declare(strict_types=1);

namespace Sports\Betting\Views;

abstract class AbstractView
{
    abstract public function render(): void;
}
