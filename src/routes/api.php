<?php

use App\Http\Router;

$router = new Router();

$router->get('/questions', 'QuestionController@index');
$router->post('/questions/respond', 'QuestionController@respond');
$router->post('/questions/image', 'QuestionImageController@respond');
