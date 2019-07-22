<?php
namespace App\Base;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class Framework
{
    protected $dispatcher;
    protected $matcher;
    protected $controllerResolver;
    protected $argumentResolver;

    public function __construct(
        UrlMatcherInterface $matcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver
    ) {
        $this->matcher            = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver   = $argumentResolver;
        $this->dispatcher         = new EventDispatcher;
        $this->events             = [];
    }

    public function handle(Request $request)
    {
        $this->matcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments  = $this->argumentResolver->getArguments($request, $controller);
            $response = call_user_func_array($controller, $arguments);

        } catch (ResourceNotFoundException $exception) {
            $response = new Response('Not Found', 404);
        } catch (\Exception $exception) {
            $response = new Response('An error occurred', 500);
        }

        if ($this->events) {
            foreach ($this->events as $name => $event) {
                $this->dispatcher->dispatch($name, $event);
            }
        }

        $this->dispatcher->dispatch('response', new \App\Events\ResponseEvent($response, $request));

        return $response;
    }

    public function addEvent($name, $event)
    {
        $this->events[$name] = $event;

        return $this;
    }

    /** Events **/
    public function addSubscriber($subscribers=array())
    {
        if ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $this->dispatcher->addSubscriber($subscriber);
            }
        }

        return $this;
    }
}