<?php

// erros:
// 101: Invalid route or this request method is not allowed
// 102: Invalid parameters for this route (unique)
// 103: Invalid parameters for this route (pair)
// 104: Invalid Action
// 105: Invalid Controller
// 106: Authentication Token invalid.

namespace route;

class Route {

    private $requestPath;
    private $method;
    private $definedPath = [];

    function __construct() {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 1');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }

        $this->requestPath = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    private function clearPath($path) {
        return trim($path, '/');
    }

    private function setRoute($method, $path, $config) {
        $path = $this->clearPath($path);

        if (empty($config['controller'])) {
            $config['controller'] = $path;
        }
        if (empty($config['requestIndex'])) {
            $config['requestIndex'] = 'input';
        }
        if (empty($config['mode'])) {
            $config['mode'] = 'unique';
        }

        if (empty($config['action'])) {
            $config['action'] = strtolower($method);
        }

        if (empty($config['params'])) {
            $config['params'] = array();
        }
        
        if (empty($config['requestHeaders'])) {
            $config['requestHeaders'] = array();
        }

        $this->definedPath[$method][$path] = $config;
    }

    public function get($path, $config) {
        $this->setRoute('GET', $path, $config);
    }

    public function post($path, $config) {
        $this->setRoute('POST', $path, $config);
    }

    public function put($path, $config) {
        $this->setRoute('PUT', $path, $config);
    }

    public function delete($path, $config) {
        $this->setRoute('DELETE', $path, $config);
    }

    public function clearRequestPath($path) {

        $rs = [];

        $t = explode('/', $path);

        foreach ($t as $a) {
            $b = trim($a);
            if (!empty($b)) {
                array_push($rs, $b);
            }
        }

        return array(
            'path' => array_shift($rs),
            'size' => count($rs),
            'indexes' => $rs
        );
    }

    public function init() {

        $path = $this->clearRequestPath($this->requestPath);

        if (
                isset($this->definedPath[$this->method][$path['path']]) &&
                !empty($this->definedPath[$this->method][$path['path']])
        ) {
            $match = $this->definedPath[$this->method][$path['path']];
        } else {
            throw new \exceptions\restException("Invalid route or this request method is not allowed", 405, 101);
        }

        $this->run($match, $path);
    }

    private function run($route, $path) {
        $mode = $route['mode'];
        $requestIndex = $route['requestIndex'];
    
        $params = [];
        if ($mode === 'unique') {
            $len = count($route['params']);

            if ($len !== $path['size']) {
                throw new \exceptions\restException("Invalid parameters for this route.", 400, 102);
            }

            for ($x = 0; $x < $len; $x++) {
                $index = $route['params'][$x];
                $value = $path['indexes'][$x];
                $params[$index] = $value;
            }
        } elseif ($mode === 'pair') {
            if ($path['size'] % 2 !== 0) {
                throw new \exceptions\restException("Invalid parameters for this route.", 400, 103);
            }
            for ($x = 0; $x < $path['size']; $x = $x + 2) {
                $index = $path['indexes'][$x];
                $value = $path['indexes'][$x + 1];
                $params[$index] = $value;
            }
        }

        if ('GET' !== $this->method) {
            $params[$requestIndex] = $this->getInputParameters();
        }
        
        if(!empty($route['requestHeaders'])){
            $headers = getallheaders();
            foreach ($route['requestHeaders'] as $header){
                if(empty($headers[$header])){
                    throw new \exceptions\restException("Authentication Token invalid.", 401, 106);
                }
                $params['headers'][$header] = $headers[$header];
            }
        }

        $controller = "\controllers\\" . $route['controller'];
        $action = $route['action'];

        if (class_exists($controller)) {
            $c = new $controller();
        } else {
            throw new \Exception('Invalid controller', 105);
        }

        if (method_exists($c, $action)) {
            $c->$action($params);
        } else {
            throw new \Exception('Invalid action', 104);
        }
    }

    private function getInputParameters() {
        if (isset($_POST) && !empty($_POST)) {
            return $_POST;
        }

        $input = file_get_contents('php://input');
        $request = json_decode($input);

        if (null !== $request) {
            return (array) $request;
        }

        return null;
    }

}
