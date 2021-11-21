<?php
/**
 * Base controller class
 *
 * */
declare(strict_types=1);

namespace Sports\Betting\Controllers;

use Sports\Betting\Models\AbstractModel;
use Sports\Betting\Views\AbstractView;
use Exception;
use ReflectionClass;

abstract class AbstractController
{
    protected function loadModel(string $model, array $args = []): AbstractModel
    {
        $model_class = $this->_getReflectionSubClass(
            'Sports\Betting\Models\AbstractModel',
            'Sports\Betting\Models\\' . $model
        );

        return $model_class->newInstance($args);
    }

    protected function loadView(string $view, array $args = []): AbstractView
    {
        $view_class = $this->_getReflectionSubClass(
            'Sports\Betting\Views\AbstractView',
            'Sports\Betting\Views\\' . $view
        );
        return $view_class->newInstance($args);
    }

    protected function getContext(string $controller, array $args = []): self
    {
        $controller_class = $this->_getReflectionSubClass(
            'Sports\Betting\Controllers\AbstractController',
            'Sports\Betting\Controllers\\' . $controller
        );

        return $controller_class->newInstance($args);
    }


    private function _getReflectionSubClass(string $parent_class_name, $sub_class_name): ReflectionClass
    {
        $parent_class = new ReflectionClass($parent_class_name);
        $sub_class = new ReflectionClass($sub_class_name);
        if (! $sub_class->isSubclassOf($parent_class)) {
            throw new Exception(
                "Error: {$sub_class_name} isn't sub class {$parent_class_name}"
            );
        }
        return $sub_class;
    }
}
