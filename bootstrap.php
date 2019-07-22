<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;

$request = Request::createFromGlobals();
$routes  = include __DIR__.'/routes/web.php';
$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

$controllerResolver = new HttpKernel\Controller\ControllerResolver();
$argumentResolver   = new HttpKernel\Controller\ArgumentResolver();

$framework = new \App\Base\Framework($matcher, $controllerResolver, $argumentResolver);
$response  = $framework->addSubscriber([new \App\Listeners\GoogleListener()])->handle($request);

