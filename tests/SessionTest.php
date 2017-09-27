<?php

namespace Tests;

use Bavix\Context\Container;
use Bavix\Context\Session;
use Bavix\Helpers\Str;
use Bavix\Tests\Unit;

class SessionTest extends Unit
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $class = Session::class;

    public function setUp()
    {
        parent::setUp();

        $this->password  = Str::random();
        $this->container = $this->container($this->password);
    }

    /**
     * @param array ...$args
     *
     * @return Container
     */
    protected function container(...$args): Container
    {
        $class = $this->class;

        return new $class(...$args);
    }

    /**
     * @runInSeparateProcess
     */
    public function testWithoutConfigure()
    {
        $this->container = $this->container();

        $this->assertNull($this->container->get('test'));
        $this->container->set('test', 'test');

        $this->assertSame(
            $this->container->get('test'),
            'test'
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testArrayAccess()
    {
        $str = Str::random();

        $this->assertNull($this->container['test']);
        $this->container['test'] = $str;

        $this->assertTrue(isset($this->container['test']));
        $this->assertSame($this->container['test'], $str);

        unset($this->container['test']);
        $this->assertNull($this->container['test']);
        $this->assertFalse(isset($this->container['test']));
    }

    /**
     * @runInSeparateProcess
     */
    public function testMagicMethods()
    {
        $str = Str::random();

        $this->assertNull($this->container->test);
        $this->container->test = $str;

        $this->assertTrue(isset($this->container->test));
        $this->assertSame($this->container->test, $str);

        unset($this->container->test);
        $this->assertNull($this->container->test);
        $this->assertFalse(isset($this->container->test));
    }

    /**
     * @runInSeparateProcess
     */
    public function testNotEncryptEmptyRows()
    {
        $this->container = $this->container();
        $this->testEmptyRows();
    }

    /**
     * @runInSeparateProcess
     */
    public function testEmptyRows()
    {
        $this->container->set('hello', 'world');

        $ref      = new \ReflectionClass($this->container);
        $property = $ref->getProperty('rows');
        $property->setAccessible(true);
        $property->setValue($this->container, []);

        $this->assertSame($this->container->get('hello'), 'world');
    }

    /**
     * @runInSeparateProcess
     */
    public function testCleanup()
    {
        $rows = [];

        foreach (\range(1, 50) as $item)
        {
            $rows[Str::random($item)] = Str::random();
            end($rows);

            $this->container->set(key($rows), current($rows));
        }

        $this->assertArraySubset(
            $rows,
            $this->container->export()
        );

        $this->assertJsonStringEqualsJsonString(
            \json_encode($rows),
            (string)$this->container
        );

        $this->container->cleanup();

        $this->assertArraySubset([], $this->container->export());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIterator()
    {
        foreach (\range(1, 50) as $item)
        {
            $this->container->set($item, $item << 1);
        }

        foreach ($this->container as $key => $value)
        {
            $this->assertSame($value, $key << 1);
        }
    }

}
