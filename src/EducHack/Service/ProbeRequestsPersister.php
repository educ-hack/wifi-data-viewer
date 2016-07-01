<?php

namespace EducHack\Service;

use Doctrine\Common\Persistence\ObjectManager;
use EducHack\Model\Device;

class ProbeRequestsPersister
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param string $logs
     */
    public function persistLogs($logs)
    {
        foreach (json_decode($logs) as $probe) {
            $device = $this->om->getRepository('EducHack:Device')->findOneByMac($probe->mac);

            if (null === $device) {
                $device = new Device();

                $device->setMac($probe->mac);

                $this->om->persist($device);
            }
        }

        $this->om->flush();
    }
}
