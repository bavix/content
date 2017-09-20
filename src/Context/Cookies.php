<?php

namespace Bavix\Context;

class Cookies extends Container
{

    /**
     * @var Configure
     */
    protected $configure;

    /**
     * Cookie constructor.
     *
     * @param null|string $password
     * @param Configure   $configure
     */
    public function __construct(?string $password = null, Configure $configure = null)
    {
        parent::__construct($password);
        $this->store = $_COOKIE;

        $this->configure = $this->load($configure);
    }

    /**
     * @return null|string
     */
    public function sessionId(): ?string
    {
        return $this->store['PHPSESSID'] ?? null;
    }

    /**
     * @return Configure
     */
    public function configure(): Configure
    {
        return $this->configure;
    }

    /**
     * @param Configure $configure
     *
     * @return Configure
     */
    protected function load(?Configure $configure): Configure
    {
        if (!$configure)
        {
            $configure = new Configure();
        }

        return $configure;
    }

    /**
     * @param string         $name
     * @param mixed          $data
     * @param Configure|null $configure
     */
    public function set(string $name, $data, Configure $configure = null): void
    {
        parent::set($name, $data);
        $configure = $configure ?: $this->configure;
        \setcookie($name, $this->store[$name], ...$configure->asArray());
    }

    /**
     * @param string $name
     */
    public function remove(string $name): void
    {
        $this->set($name, '', $this->configure()->modify([
            'expire' => 0
        ]));

        parent::remove($name);
    }

}
