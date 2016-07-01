<?php

namespace EducHack\Service;

use Doctrine\Common\Persistence\ObjectManager;
use EducHack\Model\Device;
use EducHack\Model\SSID;
use EducHack\Model\Sniffer;
use EducHack\Model\CDNHit;

class AccessPersister
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
        $jsonDecode = json_decode($logs);

        if (null === $jsonDecode) {
            return;
        }

        foreach (json_decode($logs) as $log) {
            $device = $this->om->getRepository('EducHack:Device')->findOneByMac($log->mac);
            $ssid = $this->om->getRepository('EducHack:SSID')->findOneByName($log->served_ssid);
            $sniffer = $this->om->getRepository('EducHack:Sniffer')->findOneByName($log->sniffer_id);

            if (null === $device) {
                $device = new Device();
                $device->setMac($log->mac);
                $this->om->persist($device);
            }

            if (null === $ssid) {
                $ssid = new SSID();
                $ssid->setName($log->served_ssid);
                $this->om->persist($ssid);
            }

            if (null === $sniffer) {
                $sniffer = new Sniffer();
                $sniffer->setName($log->sniffer_id);
                $this->om->persist($sniffer);
            }

            try {
                $datetime = new \DateTime($this->sanitizeDateTime($log->time));
            } catch (\Exception $e) {
                $datetime = new \DateTime();
            }

            $cdnHit = new CDNHit();

            $cdnHit
                    ->setDevice($device)
                    ->setSsid($ssid)
                    ->setSniffer($sniffer)
                    ->setDomain($log->requested_domain)
                    ->setTime($datetime)
            ;

            $this->om->persist($cdnHit);
            $this->om->flush();
        }
    }

    public function sanitizeDateTime($datetime)
    {
        return substr($datetime, 0, 15);
    }
}
