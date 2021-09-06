<?php

namespace Imanghafoori\Decorator;

use Illuminate\Support\Str;

class Decorator
{
    /**
     * All of the decorators for method calls.
     *
     * @var array
     */
    protected $globalDecorators = [];

    /**
     * All of the decorator names and definitions.
     *
     * @var array
     */
    protected $decorations = [];

    /**
     * Defines a new decorator with name.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return void
     */
    public function define($name, $callback)
    {
        $this->globalDecorators[$name] = $callback;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getGlobalDecorator($name)
    {
        return $this->globalDecorators[$name] ?? null;
    }

    public function getDecorationsFor($callback)
    {
        return $this->decorations[$callback] ?? [];
    }

    /**
     * Decorates a callable with a defined decorator name.
     *
     * @param string $callback
     * @param mixed  $decorator
     *
     * @return void
     */
    public function decorate($callback, $decorator)
    {
        $this->decorations[$callback][] = $decorator;
    }

    /**
     * Calls a class@method with it's specified decorators.
     *
     * @param string      $callback
     * @param array       $parameters
     * @param string|null $defaultMethod
     *
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null)
    {
        if (is_array($callback)) {
            $callback = $this->normalizeMethod($callback);
        }

        $decorators = $this->getDecorationsFor($callback);
        $callback = $this->decorateWith($callback, $decorators);
        $parameters = $this->getCallParams($callback, $parameters);

        return app()->call($callback, $parameters, $defaultMethod);
    }

    public function unDecorate($decorated, $decorator = null)
    {
        if (is_null($decorator)) {
            unset($this->decorations[$decorated]);
        } else {
            $this->decorations[$decorated] = array_diff($this->decorations[$decorated], [$decorator]);
        }
    }

    private function normalizeMethod($callback)
    {
        $class = is_string($callback[0]) ? $callback[0] : get_class($callback[0]);

        return "{$class}@{$callback[1]}";
    }

    /**
     * @param $callable
     * @param $decorators
     *
     * @return mixed
     */
    public function decorateWith($callable, array $decorators)
    {
        foreach ($decorators as $decorator) {
            if (is_string($decorator) and !Str::contains($decorator, '@')) {
                $decorator = $this->globalDecorators[$decorator];
            }

            is_array($decorator)
                ? $params = $this->getCallParams($this->normalizeMethod($decorator), [$callable])
                : $params = $this->getCallParams($decorator, [$callable]);

            $callable = app()->call($decorator, $params);
        }

        return $callable;
    }

    /**
     * @param  $callable
     * @param  $decorators
     * @param  $params
     *
     * @return mixed
     */
    public function callOnlyWith($callable, $decorators, $params)
    {
        $callable = $this->decorateWith($callable, $decorators);

        return \App::make($callable, $params);
    }

    /**
     * get App::Call Callable Parameters.
     *
     * @param $callable
     * @param array $params
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    public function getCallParams($callable, array $params): array
    {
        if (is_callable($callable)) {
            $argName = get_func_argNames($callable);
        } else {
            $class = explode('@', $callable);
            $argName = get_method_argNames($class[0], $class[1]);
        }
        $parameters = array_map(function ($MArgName, $Parameters) use ($argName) {
            return [$MArgName ?? $argName[count($argName) - 1]=>$Parameters];
        }, $argName, $params);

        count($parameters) == 1
            ? $parameters = $parameters[0]
            : $parameters = array_merge_recursive($parameters[0], $parameters[1]);

        return $parameters;
    }
}
