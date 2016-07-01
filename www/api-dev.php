<?php

require_once __DIR__.'/../vendor/autoload.php';

\Symfony\Component\Debug\Debug::enable();

$app = new EducHack\Application(array(
    'project.root' => dirname(__DIR__),
    'debug' => true,
));

$app->run();
