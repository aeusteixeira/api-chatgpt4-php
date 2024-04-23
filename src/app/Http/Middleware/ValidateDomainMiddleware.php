<?php 

namespace App\Http\Middleware;

class ValidateDomainMiddleware {
    private $allowedOrigins;

    public function __construct() {
        $this->allowedOrigins = DOMAINS;
    }

    public function checkDomain($origin) {
        $remoteIP = $_SERVER['REMOTE_ADDR'];
    
        $host = $origin ? parse_url($origin, PHP_URL_HOST) : null;
        if (!in_array('*', $this->allowedOrigins, true) && 
            (($host && !in_array($host, $this->allowedOrigins, true)) && 
            !in_array($remoteIP, $this->allowedOrigins, true))) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Access denied. Your IP or domain is not allowed.']);
            exit;
        }
    }
    
}