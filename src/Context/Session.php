<?php

namespace Bavix\Context;

class Session extends Container
{

    /**
     * Session constructor.
     *
     * @param string $password
     */
    public function __construct(?string $password = null)
    {
        \PHP_SAPI === 'cli' OR \session_id() OR \session_start();
        parent::__construct($password);
        $this->store = &$_SESSION;
    }

}
