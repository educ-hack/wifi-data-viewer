<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'vendor/autoload.php';

$em = EntityManagerFactory::create();

return ConsoleRunner::createHelperSet($em);
