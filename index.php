<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Router;

$router = new Router();

$router->get('/questions', 'QuestionController@index');
$router->post('/questions/respond', 'QuestionController@respond');

$router->resolve();
