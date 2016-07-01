<?php

namespace EducHack\Service;

use Doctrine\Common\Persistence\ObjectManager;
use EducHack\Model\Device;
use EducHack\Model\SSID;
use EducHack\Model\DeviceSSID;
use EducHack\Model\Position;
use EducHack\Model\Sniffer;

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
            $ssid = $this->om->getRepository('EducHack:SSID')->findOneByName($probe->requested_ssid);
            $deviceSSID = $this->om->getRepository('EducHack:DeviceSSID')->findOneByMacAndSSID(
                $probe->mac,
                $probe->requested_ssid
            );
            $sniffer = $this->om->getRepository('EducHack:Sniffer')->findOneByName($probe->sniffer_id);

            if (null === $device) {
                $device = new Device();
                $device->setMac($probe->mac);
                $this->om->persist($device);
            }

            if (null === $ssid) {
                $ssid = new SSID();
                $ssid->setName($probe->requested_ssid);
                $this->om->persist($ssid);
            }

            if (null === $deviceSSID) {
                $deviceSSID = new DeviceSSID();
                $deviceSSID
                    ->setDevice($device)
                    ->setSsid($ssid)
                ;
                $this->om->persist($deviceSSID);
            }

            if (null === $sniffer) {
                $sniffer = new Sniffer();
                $sniffer->setName($probe->sniffer_id);
                $this->om->persist($sniffer);
            }

            $position = new Position();

            $position
                ->setDevice($device)
                ->setSniffer($sniffer)
                ->setNoise($probe->noise)
                ->setDatetime(new \DateTime($probe->time))
            ;

            $this->om->persist($position);
        }

        $this->om->flush();
    }
}
