<?php
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;

$routes = new Routing\RouteCollection();
$routes->add('hello', new Routing\Route('/hello/{name}', [
	'name' => 'World',
	'_controller' => '\App\Controllers\Controller::index',
]));

return $routes;