<?php

if (!function_exists('callWithDecorators')) {
    function callWithDecorators($callback, $parameters)
    {
    }
}
if (!function_exists('get_func_argNames')) {
    /**
     * @throws ReflectionException
     */
    function get_func_argNames($funcName)
    {
        $f = new ReflectionFunction($funcName);
        $result = [];
        foreach ($f->getParameters() as $param) {
            $result[] = $param->name;
        }

        return $result;
    }
}

if (!function_exists('get_method_argNames')) {
    /**
     * @throws ReflectionException
     */
    function get_method_argNames($className, $methodName)
    {
        $f = new ReflectionMethod($className, $methodName);
        $result = [];
        foreach ($f->getParameters() as $param) {
            $result[] = $param->name;
        }

        return $result;
    }
}
