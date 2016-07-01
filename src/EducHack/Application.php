<?php

namespace EducHack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * {@InheritDoc}
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->registerDoctrine();
        $this->registerServices();
        $this->registerRoutes();
    }

    /**
     * Register doctrine DBAL and ORM
     */
    private function registerDoctrine()
    {
        $this->registerDoctrineDBAL();
        $this->registerDoctrineORM();
    }

    /**
     * Register doctrine DBAL
     */
    private function registerDoctrineDBAL()
    {
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver' => 'pdo_pgsql',
                'dbname' => 'localhost',
                'host' => 'educhack',
                'user' => 'educhack',
                'password' => 'educhack'
            ),
        ));
    }

    /**
     * Register and configure doctrine ORM
     */
    private function registerDoctrineORM()
    {
        $this->register(new \Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            'orm.proxies_dir' => $this['project.root'].'/var/cache/doctrine/proxies',
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'yml',
                        'namespace' => 'EducHack\Model',
                        'path' => $this['project.root'].'/src/EducHack/Mapping',
                        'alias' => 'EducHack',
                    ),
                ),
            ),
        ));
    }

    private function registerServices()
    {
        $this['educhack.probe_requests_persister'] = function () {
            return new Service\ProbeRequestsPersister($this['orm.em']);
        };
    }

    private function registerRoutes()
    {
        $this->post('api/prob', function (Request $request) {
            $this['educhack.probe_requests_persister']->persistLogs($request->getContent());

            return new Response('');
        });

        $this->get('/', function () {
            return $this['twig']->render('index.twig', array(
                'probe_requests_since' => ['nb_hour' => 2, 'count' => 56],
                'probe_requests_by_phone_brand' => ['HTC' => 5, 'iPhone' => 10, 'Autres' => 13]
            ));
        });
    }

}
