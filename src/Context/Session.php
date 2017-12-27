<?php

namespace Bavix\Context;

class Session extends Container
{

    /**
     * Session constructor.
     *
     * @param string $password
     * @param array  $options
     */
    public function __construct(?string $password = null, array $options = [])
    {
        \PHP_SAPI === 'cli' OR \session_id() OR \session_start($options);

        parent::__construct($password);
        $this->store = &$_SESSION;
    }

}
