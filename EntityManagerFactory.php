<?php

class EntityManagerFactory
{
    public static function create()
    {
        $params = array(
            'driver' => 'pdo_sqlite',
            'path' => 'var/educhack.sqlite',
        );

        $driver = new \Doctrine\ORM\Mapping\Driver\YamlDriver(array('Mapping'));
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataDriverImpl($driver);
        $config->setProxyDir('var/cache');
        $config->setProxyNamespace('Model');

        $conn = \Doctrine\DBAL\DriverManager::getConnection($params, $config);

        return \Doctrine\ORM\EntityManager::create($conn, $config);
    }
}
