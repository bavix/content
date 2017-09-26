<?php

namespace Tests;

use Bavix\Context\Configure;
use Bavix\Slice\Slice;
use Bavix\Tests\Unit;
use Carbon\Carbon;

class ConfigureTest extends Unit
{

    /**
     * @var Configure
     */
    protected $default;

    public function setUp()
    {
        parent::setUp();

        $this->default = new Configure();
    }

    public function testDefault()
    {
        $this->assertSame(
            $this->default->asArray(),
            [
                \time() + $this->default->expire,
                '/',
                null,
                false,
                false
            ]
        );
    }

    protected function getArray()
    {
        return [
            'expire' => 60,
            'domain' => 'example.com',
            'secure' => true,
            'path' => '/api',
            'httpOnly' => false,
        ];
    }

    public function testConstruct()
    {
        $configure = new Configure([
            'expire' => $this->default->expire
        ]);

        $this->assertEquals($configure, $this->default);
    }

    public function testModifyArray()
    {
        $configure = $this->default->modify($this->getArray());

        $this->assertSame(
            $configure->asArray(),
            [
                \time() + $this->getArray()['expire'],
               $this->getArray()['path'],
               $this->getArray()['domain'],
               $this->getArray()['secure'],
               $this->getArray()['httpOnly']
            ]
        );

        // check default not modify
        $this->testDefault();
    }

    public function testModifySlice()
    {
        $slice = new Slice($this->getArray());
        $configure = $this->default->modify($slice);

        $this->assertSame(
            $configure->asArray(),
            [
                \time() + $slice->getData('expire'),
                $slice->getData('path'),
                $slice->getData('domain'),
                $slice->getData('secure'),
                $slice->getData('httpOnly')
            ]
        );

        // check default not modify
        $this->testDefault();
    }

    public function testModifySliceCarbon()
    {
        $slice = new Slice($this->getArray());
        $slice->expire = Carbon::now()->addMonth();

        $configure = $this->default->modify($slice);

        $this->assertSame(
            $configure->asArray(),
            [
                $slice->getData('expire')->timestamp,
                $slice->getData('path'),
                $slice->getData('domain'),
                $slice->getData('secure'),
                $slice->getData('httpOnly')
            ]
        );

        // check default not modify
        $this->testDefault();
    }

    /**
     * @expectedException \Bavix\Exceptions\Invalid
     */
    public function testGetPropertyNone()
    {
        $this->default->propertyNone;
    }

    /**
     * @expectedException \Bavix\Exceptions\Invalid
     */
    public function testSetPropertyNone()
    {
        $this->default->propertyNone = 1;
    }

    /**
     * @expectedException \Bavix\Exceptions\Runtime
     */
    public function testSetReadOnly()
    {
        $this->default->expire = 1;
    }

    public function testPropertyExists()
    {
        $this->assertTrue(isset($this->default->expire));
        $this->assertFalse(isset($this->default->false));
    }

}
