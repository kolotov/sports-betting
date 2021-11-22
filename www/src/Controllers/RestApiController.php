<?php

declare(strict_types=1);

namespace Sports\Betting\Controllers;

use Error;
use Sports\Betting\Views\RestApiView;
use ReflectionMethod;

class RestApiController extends AbstractController
{
    private AbstractController $_context;
    private RestApiView $_view;
    private $_msg;
    private $_data = [];

    public function __construct()
    {
        $this->_view = $this->loadView('RestApiView');
    }

    public function process(string $entrypoint): void
    {
        $rest = $this->_getRestParams(($_SERVER['REQUEST_URI'] ?? ''), $entrypoint);
        //phpinfo();

        $method = $_SERVER['REQUEST_METHOD'] ?? '';

        $context = key($rest);
        $action = $rest[$context]['action'];
        $path_args = $rest[$context]['args'];


        //supported methods
        switch ($method) {
        case 'GET':
            $call_func = "get{$context}{$action}";
            break;

        case 'PUT':
            $call_func = "put{$context}{$action}";
            //{"bet":{"currency":"usd","sum":100,"ratio":1.1,"result":0},"winner":1}

            // get the raw POST data
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);

            if (!is_array($data)) {
                $this->_error("No data received", 400);
                return;
            }

            $bet = $data['bet'] ?? [];
            $winner = $data['winner'] ?? 0;

            $put_data = [$bet, $winner];

            break;

        default:
            $this->_error("Method not supported", 405);
            return;
            break;
        }


        try {
            $this->_context = $this->getContext($context);

            if (method_exists($this->_context, $call_func) === false) {
                $this->_error("Action not supported", 404);
                return;
            }


            //union args for function
            $args = [...$path_args, ...($put_data ?? [])];

            //check the number of accepted arguments of the function
            $r = new ReflectionMethod($this->_context, $call_func);
            if (count($args) !== $r->getNumberOfRequiredParameters()) {
                $this->_error("Invalid number of arguments", 400);
                return;
            }

            //call request method
            $this->_data = call_user_func_array(array($this->_context, $call_func), $args);
        } catch (Error $e) {
            $this->_error($e->getMessage(), $e->getCode());
            return;
        }

        $this->_success("Successfully completed");
    }

    private function _getRestParams(string $uri, $entrypoint): array
    {
        //Example /api/users/{login}/action/balance/{$currency}
        $path = parse_url($uri, PHP_URL_PATH);

        //remove all symbol except a-z 0-9
        $sanitized_path = preg_replace('/[^a-z0-9\/]/s', '', mb_strtolower($path));

        $isEntryPoint = strpos($path, $entrypoint) === 0 ? true : false;

        if (!$isEntryPoint) {
            $this->_error("It isn't entrypoint", 400);
            return [];
        }

        [,$endpoint] = explode($entrypoint, $path, 2);

        if (empty($endpoint)) {
            $this->_error("Request is empty", 400);
            return [];
        }


        $segments = explode('/action/', $endpoint);

        if (count($segments) < 2) {
            $this->_error("Bad request", 400);
            return [];
        }

        //pasrse segments
        [$context_segment, $action_segment] = $segments;

        $context = explode('/', $context_segment);
        $context_name = $context[0];
        $context_args = array_filter(array_slice($context, 1, 1));


        $action = explode('/', $action_segment);
        $action_name = $action[0];
        $action_args = array_filter(array_slice($action, 1));

        //bulding result
        $result[$context_name]['action'] = $action_name;
        $result[$context_name]['args'] = [...$context_args, ...$action_args];

        return $result;
    }


    private function _success($message, $code = 200): void
    {
        $this->_msg = ['success' => ['code' => $code, 'message' => $message]];
        $this->render();
    }


    private function _error($message, $code = null): void
    {
        $this->_msg = ['error' => ['code' => $code, 'message' => $message]];

        $this->render();
    }

    private function render(): void
    {
        $data = $this->_msg;
        $data['result'] = $this->_data['result'] ?? '';

        $this->_view->setOutput($data, ($this->_data['location'] ?? ''));
        $this->_view->render();
    }
}
