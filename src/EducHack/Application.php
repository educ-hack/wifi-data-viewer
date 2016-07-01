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
                'host' => 'localhost',
                'dbname' => 'educhack',
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

        $this['educhack.access_persister'] = function () {
            return new Service\AccessPersister($this['orm.em']);
        };
    }

    private function registerRoutes()
    {
        $this->post('api/prob', function (Request $request) {
            $this['educhack.probe_requests_persister']->persistLogs($request->getContent());

            return new Response('');
        });


        $this->post('api/access', function (Request $request) {
            $this['educhack.access_persister']->persistLogs($request->getContent());

            return new Response('');
        });

        $this->get('/', function () {
            return $this['twig']->render('index.twig', array(
                'probe_requests_since' => ['count' => $this['orm.em']->getRepository('EducHack:Position')->count()],
                'probe_requests_by_phone_brand' => $this->getBrandSharing(),
                'connexion_by_domain' => ['HTC' => 5, 'iPhone' => 10, 'Autres' => 13],
                'nb_pr' => $this->nb_pr(),
            ));
        });
    }


    private function nb_pr()
    {
        $mac_ssid = $this['orm.em']->getRepository('EducHack:DeviceSSID')->findSSIDByMac();

        $macs = array();

        foreach ($mac_ssid as $value) {
            if (array_key_exists($value['mac'], $macs)) {
                $macs[$value['mac']] []= $value['ssid'];
            } else {
                $macs [$value['mac']] = [$value['ssid']];
            }
        }

        print_r($macs);
        
        return $macs;
    }


    private function getBrandSharing()
    {
        $brands = [];
        $devices = $this['orm.em']->getRepository('EducHack:Device')->findAll();
        foreach ($devices as $device) {
            $brand = $this->getBrandForMac($device->getMac());
            $brands[$brand] = isset($brands[$brand]) ? $brands[$brand]++ : 1;
        }

        return $brands;
    }

    private function getBrandForMac($mac)
    {
        $mac = strtoupper(str_replace(':', '-', substr($mac, 0, 8)));
        chdir(__DIR__);
        $line = preg_replace('/\s+/', ' ', shell_exec('cat oui.txt|grep "'.$mac.'"'));
        if (isset(explode(' ', $line, 3)[2]))
            return explode(' ', $line, 3)[2];
        else return 'Autres';
    }

}
