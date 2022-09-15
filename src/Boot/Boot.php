<?php 

namespace Boot;

use Boot\Service\DatabaseService;
use Controller\BaseController;

class Boot
{

    public const SERVICES = [
        DatabaseService::class
    ];

    protected string $controllerName;

    //          [uri]
    // site.com/sobre
    // site.com/sobre/algo/outra-coisa
    public function __construct(string $uri)
    {   
        // removo a primeira barra
        $uri = substr($uri, 1);
        
        // recorto a string nas barras e pego a primeira palavra da uri
        $controllerName = explode('/', $uri)[0];

        // se a primeira palavra for vazia o controlador é o home
        if(empty($controllerName)) {
            $controllerName = 'Home';
        } else {
            // caso contrário o controlador sempre tem a primeira letra em maiúsculo
            $controllerName = ucfirst($controllerName);
        }

        $this->controllerName = $controllerName;

        foreach(self::SERVICES as $serviceClass) {
            (new $serviceClass)->boot();
        }
    }

    public function executeController(): string 
    {
        $controller = $this->getControllerClassOrFail();
        if(is_string($controller)) {
            return $controller;
        }

        $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        if(
            !in_array($requestMethod, ['get', 'post', 'put', 'delete', 'head']) ||
            !method_exists($controller, $requestMethod)
        ) {
            http_response_code(501);
            return '<h1>Erro 501</h1>';
        }

        return call_user_func([$controller, $requestMethod]);
    }

    private function getControllerClassOrFail(): BaseController|string
    {
        $controllerClass = 'Controller\\'.$this->controllerName.'Controller';
        if(!class_exists($controllerClass)) {
            http_response_code(404);
            return '<h1>Erro 404</h1>';
        }

        return new $controllerClass;
    }
}