<?php

namespace App\Tests\Service\Calculator;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\Product;
use App\Entity\Catalog\ProductAuctionPrice;
use App\Service\Calculator\CalculatorParamsFactory;
use PHPUnit\Framework\TestCase;

class CalculatorParamsFactoryTest extends TestCase
{
    /**
     * @var CalculatorParamsFactory
     */
    private $calculatorParamsFactory;

    /**
     * @var Material[]
     */
    private $materials;

    /**
     * @var Device[]
     */
    private $devices;

    /**
     * @var int[]
     */
    private $inventoryQtys;

    /**
     * @var int[]
     */
    private $miningAcceptableIds;

    /**
     * @var bool
     */
    private $isAuctionSellAllowed;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->calculatorParamsFactory = new CalculatorParamsFactory();

        $materialProductAuctionPrice1 = new ProductAuctionPrice();
        $materialProductAuctionPrice1->setValue(5488);
        $materialProduct1 = new Product();
        $materialProduct1
            ->setMarketplacePrice(null)
            ->setAuctionPrice($materialProductAuctionPrice1);
        $material1 = $this->createMock(Material::class);
        $material1
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5));
        $material1
            ->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($materialProduct1));

        $materialProductAuctionPrice2 = new ProductAuctionPrice();
        $materialProductAuctionPrice2->setValue(547);
        $materialProduct2 = new Product();
        $materialProduct2
            ->setMarketplacePrice(544)
            ->setAuctionPrice($materialProductAuctionPrice2);
        $material2 = $this->createMock(Material::class);
        $material2
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(2));
        $material2
            ->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($materialProduct2));

        $this->materials = [$material1, $material2];

        $deviceProductAuctionPrice1 = new ProductAuctionPrice();
        $deviceProductAuctionPrice1->setValue(9445);
        $deviceProduct1 = new Product();
        $deviceProduct1
            ->setMarketplacePrice(4857)
            ->setAuctionPrice($deviceProductAuctionPrice1);
        $device1 = $this->createMock(Device::class);
        $device1
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(26));
        $device1
            ->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($deviceProduct1));

        $deviceProductAuctionPrice2 = new ProductAuctionPrice();
        $deviceProductAuctionPrice2->setValue(10);
        $deviceProduct2 = new Product();
        $deviceProduct2
            ->setMarketplacePrice(8)
            ->setAuctionPrice($deviceProductAuctionPrice2);
        $device2 = $this->createMock(Device::class);
        $device2
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(27));
        $device2
            ->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($deviceProduct2));

        $this->devices = [$device1, $device2];

        $this->inventoryQtys = [17, 0];

        $this->miningAcceptableIds = [1, 2];

        $this->isAuctionSellAllowed = false;
    }

    public function testCreateParamsForInventory()
    {
        $calculatorParams = $this->calculatorParamsFactory->createParamsForInventory(
            $this->materials,
            $this->devices,
            $this->inventoryQtys,
            $this->isAuctionSellAllowed
        );

        $materialStockItems = $calculatorParams->getMaterialStockItems();

        $materialSource1 = $materialStockItems[0]->getSources()[0];
        $this->assertSame('auction', $materialSource1->getType());
        $this->assertSame(5488, $materialSource1->getPrice());
        $this->assertNull($materialSource1->getQty());

        $materialDestination1 = $materialStockItems[0]->getDestinations()[0];
        $this->assertSame('auction', $materialDestination1->getType());
        $this->assertSame(4939, $materialDestination1->getPrice());

        $deviceStockItems = $calculatorParams->getDeviceStockItems();

        $deviceSource1 = $deviceStockItems[0]->getSources()[0];
        $this->assertSame('auction', $deviceSource1->getType());
        $this->assertSame(9445, $deviceSource1->getPrice());
        $this->assertNull($deviceSource1->getQty());

        $deviceDestination1 = $deviceStockItems[0]->getDestinations()[0];
        $this->assertSame('marketplace', $deviceDestination1->getType());
        $this->assertSame(4857, $deviceDestination1->getPrice());
    }

    public function testCreateParamsForTrade()
    {
        $calculatorParams = $this->calculatorParamsFactory->createParamsForTrade(
            $this->materials,
            $this->devices,
            $this->isAuctionSellAllowed
        );

        $materialStockItems = $calculatorParams->getMaterialStockItems();

        $materialSource1 = $materialStockItems[0]->getSources()[0];
        $this->assertSame('auction', $materialSource1->getType());
        $this->assertSame(5488, $materialSource1->getPrice());
        $this->assertNull($materialSource1->getQty());

        $materialDestination1 = $materialStockItems[0]->getDestinations()[0];
        $this->assertSame('auction', $materialDestination1->getType());
        $this->assertSame(4939, $materialDestination1->getPrice());

        $deviceStockItems = $calculatorParams->getDeviceStockItems();

        $deviceSource1 = $deviceStockItems[0]->getSources()[0];
        $this->assertSame('auction', $deviceSource1->getType());
        $this->assertSame(9445, $deviceSource1->getPrice());
        $this->assertNull($deviceSource1->getQty());

        $deviceDestination1 = $deviceStockItems[0]->getDestinations()[0];
        $this->assertSame('marketplace', $deviceDestination1->getType());
        $this->assertSame(4857, $deviceDestination1->getPrice());
    }

    public function testCreateParamsForMining()
    {
        $calculatorParams = $this->calculatorParamsFactory->createParamsForMining(
            $this->materials,
            $this->devices,
            $this->miningAcceptableIds,
            $this->isAuctionSellAllowed
        );

        $materialStockItems = $calculatorParams->getMaterialStockItems();

        $materialSource1 = $materialStockItems[0]->getSources()[0];
        $this->assertSame('auction', $materialSource1->getType());
        $this->assertSame(5488, $materialSource1->getPrice());
        $this->assertNull($materialSource1->getQty());

        $materialDestination1 = $materialStockItems[0]->getDestinations()[0];
        $this->assertSame('auction', $materialDestination1->getType());
        $this->assertSame(4939, $materialDestination1->getPrice());

        $deviceStockItems = $calculatorParams->getDeviceStockItems();

        $deviceSource1 = $deviceStockItems[0]->getSources()[0];
        $this->assertSame('auction', $deviceSource1->getType());
        $this->assertSame(9445, $deviceSource1->getPrice());
        $this->assertNull($deviceSource1->getQty());

        $deviceDestination1 = $deviceStockItems[0]->getDestinations()[0];
        $this->assertSame('marketplace', $deviceDestination1->getType());
        $this->assertSame(4857, $deviceDestination1->getPrice());
    }

    public function testCreateParams()
    {
        $calculatorParams = $this->calculatorParamsFactory->createParams(
            $this->materials,
            $this->devices,
            [],
            [],
            $this->isAuctionSellAllowed
        );

        $materialStockItems = $calculatorParams->getMaterialStockItems();

        $materialSource1 = $materialStockItems[0]->getSources()[0];
        $this->assertSame('auction', $materialSource1->getType());
        $this->assertSame(5488, $materialSource1->getPrice());
        $this->assertNull($materialSource1->getQty());

        $materialDestination1 = $materialStockItems[0]->getDestinations()[0];
        $this->assertSame('auction', $materialDestination1->getType());
        $this->assertSame(4939, $materialDestination1->getPrice());

        $deviceStockItems = $calculatorParams->getDeviceStockItems();

        $deviceSource1 = $deviceStockItems[0]->getSources()[0];
        $this->assertSame('auction', $deviceSource1->getType());
        $this->assertSame(9445, $deviceSource1->getPrice());
        $this->assertNull($deviceSource1->getQty());

        $deviceDestination1 = $deviceStockItems[0]->getDestinations()[0];
        $this->assertSame('marketplace', $deviceDestination1->getType());
        $this->assertSame(4857, $deviceDestination1->getPrice());
    }
}
