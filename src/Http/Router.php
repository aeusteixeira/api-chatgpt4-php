<?php

namespace App\Http;

use App\Http\Request;
use ReflectionMethod;
use Exception;

class Router {
    private $routes = [];

    /**
     * Adiciona uma rota GET ao roteador.
     *
     * @param string $path O caminho da URL para a rota.
     * @param mixed $action A ação a ser executada quando a rota é acessada.
     */
    public function get(string $path, $action): void {
        $this->addRoute('GET', $path, $action);
    }

    /**
     * Adiciona uma rota POST ao roteador.
     *
     * @param string $path O caminho da URL para a rota.
     * @param mixed $action A ação a ser executada quando a rota é acessada.
     */
    public function post(string $path, $action): void {
        $this->addRoute('POST', $path, $action);
    }

    /**
     * Adiciona uma rota PUT ao roteador.
     *
     * @param string $path O caminho da URL para a rota.
     * @param mixed $action A ação a ser executada quando a rota é acessada.
     */
    public function put(string $path, $action): void {
        $this->addRoute('PUT', $path, $action);
    }

    /**
     * Adiciona uma rota DELETE ao roteador.
     *
     * @param string $path O caminho da URL para a rota.
     * @param mixed $action A ação a ser executada quando a rota é acessada.
     */
    public function delete(string $path, $action): void {
        $this->addRoute('DELETE', $path, $action);
    }

    /**
     * Adiciona uma rota ao array de rotas.
     *
     * @param string $method O método HTTP da rota.
     * @param string $path O caminho da URL para a rota.
     * @param mixed $action A ação a ser executada quando a rota é acessada.
     */
    private function addRoute(string $method, string $path, $action): void {
        $this->routes[$method][$path] = $action;
    }

    /**
     * Resolve a rota atual e executa a ação correspondente.
     */
    public function resolve(): void {
        $path = $this->getCurrentPath();
        $method = $this->getCurrentMethod();
        $request = new Request();

        $action = $this->findAction($method, $path);
        if (!$action) {
            $this->sendNotFound();
            return;
        }

        $this->executeAction($action, $request, $path);
    }

    /**
     * Obtém o caminho atual da URL.
     *
     * @return string O caminho da URL.
     */
    private function getCurrentPath(): string {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Obtém o método HTTP atual.
     *
     * @return string O método HTTP.
     */
    private function getCurrentMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Encontra a ação correspondente para o método e caminho atuais.
     *
     * @param string $method O método HTTP da requisição.
     * @param string $path O caminho da URL da requisição.
     * @return array|null A ação e os parâmetros capturados, ou null se nenhuma ação for encontrada.
     */
    private function findAction(string $method, string $path): ?array {
        foreach ($this->routes[$method] as $routePath => $action) {
            $pattern = $this->convertToPattern($routePath);
            if (preg_match($pattern, $path, $matches)) {
                return ['action' => $action, 'params' => $this->filterParams($matches)];
            }
        }
        return null;
    }

    /**
     * Converte o caminho da rota em uma expressão regular para correspondência de padrões.
     *
     * @param string $routePath O caminho da rota.
     * @return string A expressão regular correspondente.
     */
    private function convertToPattern(string $routePath): string {
        return "#^" . preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^/]+)', $routePath) . "$#";
    }

    /**
     * Filtra os parâmetros capturados da URL, mantendo apenas os nomes dos parâmetros.
     *
     * @param array $matches Os parâmetros capturados pela expressão regular.
     * @return array Os parâmetros filtrados.
     */
    private function filterParams(array $matches): array {
        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    /**
     * Executa a ação encontrada.
     *
     * @param array $action A ação e os parâmetros capturados.
     * @param Request $request O objeto Request.
     * @param string $path O caminho da URL da requisição.
     */
    private function executeAction(array $action, Request $request, string $path): void {
        if (is_string($action['action'])) {
            $this->executeControllerAction($action['action'], $request, $action['params']);
        } else {
            call_user_func_array($action['action'], [$request, ...array_values($action['params'])]);
        }
    }

    /**
     * Executa uma ação de controlador.
     *
     * @param string $action A ação no formato 'Controller@method'.
     * @param Request $request O objeto Request.
     * @param array $params Os parâmetros capturados da URL.
     */
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
    

    /**
     * Envia uma resposta 404 Not Found.
     */
    private function sendNotFound(): void {
        http_response_code(404);
        echo "404 Not Found";
    }
}
