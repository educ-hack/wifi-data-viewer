<?php

namespace EducHack\Repository;

use Doctrine\ORM\EntityRepository;
use EducHack\Model\DeviceSSID;

class DeviceSSIDRepository extends EntityRepository
{
    /**
     * @param string $mac
     * @param string $ssid
     *
     * @return DeviceSSID|null
     */
    public function findOneByMacAndSSID($mac, $ssid)
    {
        return $this->createQueryBuilder('device_ssid')
            ->leftJoin('device_ssid.device', 'device')
            ->leftJoin('device_ssid.ssid', 'ssid')
            ->andWhere('device.mac = :mac')
            ->andWhere('ssid.name = :ssid')
            ->setParameters(array(
                'mac' => $mac,
                'ssid' => $ssid,
            ))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findSSIDByMac()
    {
        return $this->getEntityManager()->getConnection()
            ->fetchAll('SELECT device.mac, ssid.name FROM devicessid LEFT JOIN device ON devicessid.device_id = device.id LEFT JOIN ssid ON devicessid.ssid_id=ssid.id GROUP BY device.mac, ssid.name');
    }
}
