<?php

namespace App\Http;

use App\Http\Middleware\ValidateDomainMiddleware;

class Request {
    private $data;
    private $validateDomainMiddleware;

    public function __construct(ValidateDomainMiddleware $validateDomainMiddleware) {
        $this->validateDomainMiddleware = $validateDomainMiddleware;
        $this->data = $this->getData();
        $this->validateDomainMiddleware->checkDomain($this->getHeader('Origin') ?: $this->getHeader('Referer'));
    }

    private function getData() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                return $_GET;
            case 'POST':
            case 'PUT':
            case 'PATCH':
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                if (strpos($contentType, 'application/json') !== false) {
                    return json_decode(file_get_contents('php://input'), true) ?? [];
                } else {
                    return $_POST;
                }
            default:
                return [];
        }
    }

    public function all() {
        return $this->data;
    }

    public function input($key, $default = null) {
        return $this->data[$key] ?? $default;
    }

    public function getHeader($header) {
        $headers = getallheaders();
        return $headers[$header] ?? null;
    }
}