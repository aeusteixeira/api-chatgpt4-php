<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/routes/api.php';

$router->resolve();
