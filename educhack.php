<?php

require_once 'vendor/autoload.php';

$em = EntityManagerFactory::create();

echo 'parsing logs'.PHP_EOL;

while (!feof(STDIN)) {
    $line = fgets(STDIN);

    if (empty($line)) {
        continue;
    }

    $parse = parseProbeRequest($line);

    echo implode(' ', array(
        $parse['mac'],
        $parse['time'],
        $parse['noise'],
        $parse['ssid'],
    )).PHP_EOL;

    persist($parse['mac'], $parse['time'], $parse['noise'], $parse['ssid']);
}

function parseProbeRequest($line) {
    $matches = null;

    preg_match('/(?P<time>[0-9:]{8}).+(?P<noise>-[0-9]+dB).+SA:(?P<mac>[0-9a-f:]+) .+ Probe Request \((?P<ssid>.*)\)/', $line, $matches);

    return $matches;
}

function persist($mac, $time, $noise, $ssid) {
    global $em;

    $device = new Model\Device();

    $device->setMac($mac);

    $em->persist($device);
    $em->flush();
}

echo 'end'.PHP_EOL;
