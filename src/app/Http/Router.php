<?php

namespace App\Http;

use App\Http\Middleware\ValidateDomainMiddleware;
use App\Http\Request;
use ReflectionMethod;
use Exception;

class Router {
    private $routes = [];

    public function get(string $path, $action): void {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, $action): void {
        $this->addRoute('POST', $path, $action);
    }

    public function put(string $path, $action): void {
        $this->addRoute('PUT', $path, $action);
    }

    public function delete(string $path, $action): void {
        $this->addRoute('DELETE', $path, $action);
    }

    private function addRoute(string $method, string $path, $action): void {
        $this->routes[$method][$path] = $action;
    }

    public function resolve(): void {
        $path = $this->getCurrentPath();
        $method = $this->getCurrentMethod();
        $validateDomainMiddleware = new ValidateDomainMiddleware();
        $request = new Request($validateDomainMiddleware);

        $action = $this->findAction($method, $path);
        if (!$action) {
            $this->sendNotFound();
            return;
        }

        $this->executeAction($action, $request, $path);
    }

    private function getCurrentPath(): string {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function getCurrentMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function findAction(string $method, string $path): ?array {
        foreach ($this->routes[$method] as $routePath => $action) {
            $pattern = $this->convertToPattern($routePath);
            if (preg_match($pattern, $path, $matches)) {
                return ['action' => $action, 'params' => $this->filterParams($matches)];
            }
        }
        return null;
    }

    private function convertToPattern(string $routePath): string {
        return "#^" . preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^/]+)', $routePath) . "$#";
    }

    private function filterParams(array $matches): array {
        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    private function executeAction(array $action, Request $request, string $path): void {
        if (is_string($action['action'])) {
            $this->executeControllerAction($action['action'], $request, $action['params']);
        } else {
            call_user_func_array($action['action'], [$request, ...array_values($action['params'])]);
        }
    }

    private function executeControllerAction(string $action, Request $request, array $params) {
        [$controllerName, $methodName] = explode('@', $action);
        $controller = "App\\Controllers\\$controllerName";
        if (!class_exists($controller)) {
            throw new Exception("Controller: $controller não encontrado");
        }
        $controllerInstance = new $controller();
        if (!method_exists($controllerInstance, $methodName)) {
            throw new Exception("Método: $methodName não encontrado no controller: $controller");
        }
    
        $reflectionMethod = new ReflectionMethod($controller, $methodName);
        $methodParams = $reflectionMethod->getParameters();
        $args = [];
        foreach ($methodParams as $param) {
            $paramName = $param->getName();
            $paramType = $param->getType() ? $param->getType()->getName() : null;
            if ($paramType === Request::class) {
                $args[] = $request;
            } elseif (isset($params[$paramName])) {
                $args[] = $params[$paramName];
            }
        }
    
        call_user_func_array([$controllerInstance, $methodName], $args);
    }
    

    private function sendNotFound(): void {
        http_response_code(404);
        echo "404 Not Found";
    }
}
