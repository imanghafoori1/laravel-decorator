<?php

use Imanghafoori\Decorator\Decorator;

class DecoratorTest extends TestCase
{
    public function testSimpleDecorator()
    {
        $decorator = new Decorator();

        $decorator->define('stringifyResult', ResultCasterDecorator::class.'@_toString');

        $decorator->decorate(Calculator::class.'@add', 'stringifyResult');
        $result = $decorator->call(Calculator::class.'@add', [-10, -10]);

        $this->assertIsString($result);
        $this->assertEquals('-20', $result);

        $result = $decorator->call([Calculator::class, 'add'], [-10, -10]);
        $this->assertIsString($result);
        $this->assertEquals('-20', $result);

        $result = $decorator->call([new Calculator(), 'add'], [-10, -10]);
        $this->assertIsString($result);
        $this->assertEquals('-20', $result);

        $decorator->unDecorate(Calculator::class.'@add', 'stringifyResult');
        $result = $decorator->call(Calculator::class.'@add', [-10, -10]);
        $this->assertEquals(-20, $result);
        $this->assertIsInt($result);

        $decorator->decorate(Calculator::class.'@add', 'stringifyResult');

        $decorator->unDecorate(Calculator::class.'@add');
        $result = $decorator->call(Calculator::class.'@add', [-10, -10]);
        $this->assertEquals(-20, $result);
        $this->assertIsInt($result);
    }

    public function testDecoratedWithCallbacks()
    {
        $decorator = new Decorator();

        $decorator->decorate(Calculator::class.'@add', $this->getResultCasterDecorator());

        $result = $decorator->call([new Calculator(), 'add'], [-10, -10]);
        $this->assertIsString($result);
        $this->assertEquals('-20', $result);

        $decorator = new Decorator();

        $decorator->decorate(Calculator::class.'@add', ResultCasterDecorator::class.'@toStringDecorator');

        $result = $decorator->call([new Calculator(), 'add'], [-10, -10]);
        $this->assertIsString($result);
        $this->assertEquals('-20', $result);

        $decorator = new Decorator();

        $decorator->decorate(Calculator::class.'@addToStr', [ResultCasterDecorator::class, 'toInt']);

        $result = $decorator->call([new Calculator(), 'addToStr'], [-10, -10]);
        $this->assertIsInt($result);
        $this->assertEquals(-20, $result);

        $decorator = new Decorator();
        $decorator->decorate(Calculator::class.'@addToStr', ResultCasterDecorator::class.'@toInt');

        $result = $decorator->call([new Calculator(), 'addToStr'], [-10, -10]);
        $this->assertIsInt($result);
        $this->assertEquals(-20, $result);
    }

    public function testSimpleDecoratorOnInterface()
    {
        $decorator = new Decorator();

        $decorator->define('stringifyResult', $this->getResultCasterDecorator());

        $decorator->decorate(ICalculator::class.'@add', 'stringifyResult');
        app()->bind(ICalculator::class, Calculator::class);

        $result = $decorator->call(ICalculator::class.'@add', [10, 10]);
        $this->assertIsString($result);
        $this->assertEquals('20', $result);
    }

    public function testTwoDecorators()
    {
        $decorator = new Decorator();

        $stringifyDecorator = function ($decorated) {
            return function (...$params) use ($decorated) {
                return (string) app()->call($decorated, app('decorator')->getCallParams($decorated, $params[0]));
            };
        };

        $intifyParamsDecorator = function ($decorated) {
            return function ($x, $y) use ($decorated) {
                return app()->call($decorated, app('decorator')->getCallParams($decorated, [(int) $x, (int) $y]));
            };
        };

        $decorator->define('stringifyResult', $stringifyDecorator);
        $decorator->define('intifyParams', $intifyParamsDecorator);

        $decorator->decorate(Calculator::class.'@add', 'intifyParams');
        $decorator->decorate(Calculator::class.'@add', 'stringifyResult');

        $result = $decorator->call(Calculator::class.'@add', ['-10', '-10']);

        $this->assertIsString($result);
        $this->assertEquals('-20', $result);
    }

    public function testMultipleDecorators()
    {
        $decorator = new Decorator();

        $decorator->define('minParam:-20', function ($callback) {
            return function ($x, $y) use ($callback) {
                $x = ($x < -20) ? -20 : $x;
                $y = ($y < -20) ? -20 : $y;

                return app()->call($callback, app('decorator')->getCallParams($callback, [$x, $y]));
            };
        });

        $decorator->define('maxResult:20', $this->maxResult(20));

        $decorator->define('stringifyResult', $this->getResultCasterDecorator());

        $decorator->decorate(Calculator::class.'@add', 'minParam:-20');
        $decorator->decorate(Calculator::class.'@add', 'maxResult:20');
        $decorator->decorate(Calculator::class.'@add', 'stringifyResult');

        $result = $decorator->call(Calculator::class.'@add', ['x' => 2, 'y' => 2]);

        $this->assertEquals('4', $result);
        $this->assertIsString($result);

        $result = $decorator->call(Calculator::class.'@add', ['x' => 20, 'y' => 20]);
        $this->assertEquals('20', $result);
        $this->assertIsString($result);

        $result = $decorator->call(Calculator::class.'@add', ['x' => -200, 'y' => -200]);
        $this->assertEquals('-40', $result);
        $this->assertIsString($result);

        $result = $decorator->call(Calculator::class.'@add', ['x' => -100, 'y' => -100]);
        $this->assertEquals('-40', $result);
        $this->assertIsString($result);
    }

    /**
     * @return \Closure
     */
    public function getResultCasterDecorator()
    {
        return function ($callable) {
            return function (...$params) use ($callable) {
                return (string) app()->call($callable, app('decorator')->getCallParams($callable, $params[0]));
            };
        };
    }

    /**
     * @param int $max
     *
     * @return \Closure
     */
    private function maxResult(int $max): \Closure
    {
        return function ($callable) use ($max) {
            return function (...$params) use ($callable, $max) {
                $result = app()->call($callable, app('decorator')->getCallParams($callable, $params[0]));

                return ($result > $max) ? $max : $result;
            };
        };
    }
}

interface ICalculator
{
    public function add(int $x, int $y): int;
}

class Calculator implements ICalculator
{
    public function add(int $x, int $y): int
    {
        return $x + $y;
    }

    public function arraySum(...$x)
    {
        app()->call($this->addToStr($x, $y));

        return array_sum($x);
    }

    public function addToStr(int $x, int $y): string
    {
        return (string) ($x + $y);
    }
}

class ResultCasterDecorator
{
    public function toStringDecorator($callable)
    {
        return function (...$params) use ($callable) {
            return (string) app()->call($callable, app('decorator')->getCallParams($callable, is_array($params[0]) ? $params[0] : $params));
        };
    }

    public function minimumParamZero($callable)
    {
        return function (...$params) use ($callable) {
            foreach ($params as $i => $param) {
                if ($param < 0) {
                    $params[$i] = 0;
                }
            }

            return app()->call($callable, $params);
        };
    }

    public static function toStringStaticDecorator($callable)
    {
        return function (...$params) use ($callable) {
            return (string) app()->call($callable, $params);
        };
    }

    public static function toInt($callable)
    {
        return function (...$params) use ($callable) {
            return (int) app()->call($callable, app('decorator')->getCallParams($callable, is_array($params[0]) ? $params[0] : $params));
        };
    }

    public function _toString($callable)
    {
        return function (...$params) use ($callable) {
            return (string) app()->call($callable, ['x'=>$params[0][0], 'y'=>$params[0][1]]);
        };
    }
}
