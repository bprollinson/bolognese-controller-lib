<?php

require_once('vendor/bprollinson/bolognese-controller-api/src/MethodInvocation.class.php');
require_once('vendor/bprollinson/bolognese-controller-api/src/MethodNotFoundResponse.class.php');
require_once('vendor/bprollinson/bolognese-controller-api/src/MethodInvokedResponse.class.php');

class MethodInvoker
{
    public function invoke(MethodInvocation $methodInvocation)
    {
        $className = $methodInvocation->getClass();
        $classFileName = dirname(__FILE__) . "/{$className}.class.php";

        if (!file_exists($classFileName))
        {
            return new MethodNotFoundResponse();
        }

        require_once($classFileName);

        if (!class_exists($className, false))
        {
            return new MethodNotFoundResponse();
        }

        $method = $methodInvocation->getMethod();
        if (!method_exists($className, $method))
        {
            return new MethodNotFoundResponse();
        }

        $classInstance = new $className();
        $methodInvoked = $classInstance->$method($methodInvocation->getParameterValues(), $methodInvocation->getGetValues(), $methodInvocation->getPostValues());

        return new MethodInvokedResponse($methodInvoked);
    }
}
