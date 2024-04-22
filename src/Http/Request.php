<?php

namespace App\Http;

class Request {
    private $data;

    public function __construct() {
        $this->data = $this->getData();
    }

    private function getData() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $_GET;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                return json_decode(file_get_contents('php://input'), true) ?? [];
            } else {
                return $_POST; // Isso pode não ser necessário para PUT e PATCH, mas é mantido para compatibilidade
            }
        }
        return [];
    }

    public function all() {
        return $this->data;
    }

    public function input($key, $default = null) {
        return $this->data[$key] ?? $default;
    }
}
