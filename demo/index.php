<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

//$session = new \Bavix\Context\Session(__FILE__);
//
//var_dump($session, $session->export());
//
//$session->hello = \Bavix\Helpers\Str::random();

$cookies = new \Bavix\Context\Cookies(__FILE__);

var_dump($cookies->hello);

unset($cookies['hello']);

//$cookies->hello = 'world';

