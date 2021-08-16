<?php

namespace App\Service\Calculator\Data;

class DeviceDeal extends AbstractDeal implements DealInterface
{
    /**
     * @var int
     */
    private $deviceId;

    /**
     * @var DealSource
     */
    private $source;

    /**
     * @param int $deviceId
     * @param DealSource $source
     * @param DealDestination $destination
     */
    public function __construct(int $deviceId, DealSource $source, DealDestination $destination)
    {
        parent::__construct($destination);

        $this->deviceId = $deviceId;
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    /**
     * @return DealSource
     */
    public function getSource(): DealSource
    {
        return $this->source;
    }
}
