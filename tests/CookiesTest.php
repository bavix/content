<?php

namespace Tests;

use Bavix\Context\Configure;
use Bavix\Context\Cookies;
use Bavix\Helpers\Str;

class CookiesTest extends SessionTest
{

    /**
     * @var Cookies
     */
    protected $container;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $class = Cookies::class;

    public function setUp()
    {
        parent::setUp();

        $this->password = Str::random();

        $configure       = new Configure();
        $this->container = $this->container($this->password, $configure);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionId()
    {
        $str = Str::random();

        $this->assertNull($this->container->sessionId());
        $this->container->set('PHPSESSID', $str);

        $this->assertSame($str, $this->container->get('PHPSESSID'));
    }

}
