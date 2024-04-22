<?php

namespace App\Controllers;

abstract class Controller {
    /**
     * Envia uma resposta JSON para o cliente.
     *
     * @param mixed $data Os dados que serão codificados em JSON e enviados na resposta.
     * @param int $status O código de status HTTP para a resposta. Padrão é 200.
     * @return void
     */
    public function response($data, int $status = 200): void {
        header('Content-Type: application/json');
        http_response_code($status);

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            // Falha na conversão para JSON
            http_response_code(500);
            $json = json_encode(['error' => 'Falha na conversão dos dados para JSON'], JSON_UNESCAPED_UNICODE);
        }

        echo $json;
    }
}
