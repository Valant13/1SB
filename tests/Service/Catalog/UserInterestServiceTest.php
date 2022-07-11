<?php

namespace App\Tests\Service\Catalog;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\DeviceCraftingComponent;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\UserInterest;
use App\Entity\User\User;
use App\Repository\Catalog\DeviceRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Repository\Catalog\UserInterestRepository;
use App\Service\Catalog\UserInterestService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserInterestServiceTest extends TestCase
{
    /**
     * @var UserInterestService
     */
    private $userInterestService;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Material[]
     */
    private $materials;

    /**
     * @var Device[]
     */
    private $devices;

    /**
     * @var bool
     */
    private $isExcluded;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $interest = $this->createMock(UserInterest::class);
        $interest
            ->expects($this->any())
            ->method('getExcludedMaterialIds')
            ->will($this->returnValue([1, 5]));
        $interest
            ->expects($this->any())
            ->method('getExcludedDeviceIds')
            ->will($this->returnValue([]));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $interestRepository = $this->createMock(UserInterestRepository::class);
        $interestRepository
            ->expects($this->any())
            ->method('findOneByUser')
            ->will($this->returnValue($interest));
        $materialRepository = $this->createMock(MaterialRepository::class);
        $deviceRepository = $this->createMock(DeviceRepository::class);

        $this->userInterestService = new UserInterestService(
            $entityManager,
            $interestRepository,
            $materialRepository,
            $deviceRepository
        );

        $this->user = $this->createMock(User::class);

        $material1 = $this->createMock(Material::class);
        $material1
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5));

        $material2 = $this->createMock(Material::class);
        $material2
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(2));

        $this->materials = [$material1, $material2];

        $device1 = $this->createMock(Device::class);
        $device1
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(26));
        $device1
            ->expects($this->any())
            ->method('getCraftingComponents')
            ->will($this->returnValue($this->createCraftingComponents([4, 7, 1, 14])));

        $device2 = $this->createMock(Device::class);
        $device2
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(27));
        $device2
            ->expects($this->any())
            ->method('getCraftingComponents')
            ->will($this->returnValue($this->createCraftingComponents([4, 23, 25])));

        $this->devices = [$device1, $device2];

        $this->isExcluded = false;
    }

    public function testFilterMaterialsByExclusion()
    {
        $includedMaterials = $this->userInterestService->filterMaterialsByExclusion(
            $this->materials,
            $this->user,
            $this->isExcluded
        );

        $this->assertCount(1, $includedMaterials);
    }

    public function testFilterDevicesByExclusion()
    {
        $includedDevices = $this->userInterestService->filterDevicesByExclusion(
            $this->devices,
            $this->user,
            $this->isExcluded
        );

        $this->assertCount(1, $includedDevices);
    }

    /**
     * @param int[] $deviceIds
     * @return MockObject
     */
    private function createCraftingComponents(array $deviceIds): MockObject
    {
        $craftingComponents = [];
        foreach ($deviceIds as $deviceId) {
            $material = $this->createMock(Material::class);
            $material
                ->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($deviceId));

            $craftingComponent = $this->createMock(DeviceCraftingComponent::class);
            $craftingComponent
                ->expects($this->any())
                ->method('getMaterial')
                ->will($this->returnValue($material));

            $craftingComponents[] = $craftingComponent;
        }

        $iterator = $this->createMock(\Iterator::class);

        $collection = $this->createMock(Collection::class);
        $collection
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($this->mockIterator($iterator, $craftingComponents)));

        return $collection;
    }

    /**
     * Setup methods required to mock an iterator
     *
     * @param MockObject $iteratorMock The mock to attach the iterator methods to
     * @param array $items The mock data we're going to use with the iterator
     * @return MockObject The iterator mock
     */
    private function mockIterator(MockObject $iteratorMock, array $items): MockObject
    {
        $iteratorData = new \stdClass();
        $iteratorData->array = $items;
        $iteratorData->position = 0;

        $iteratorMock->expects($this->any())
            ->method('rewind')
            ->will(
                $this->returnCallback(
                    function() use ($iteratorData) {
                        $iteratorData->position = 0;
                    }
                )
            );

        $iteratorMock->expects($this->any())
            ->method('current')
            ->will(
                $this->returnCallback(
                    function() use ($iteratorData) {
                        return $iteratorData->array[$iteratorData->position];
                    }
                )
            );

        $iteratorMock->expects($this->any())
            ->method('key')
            ->will(
                $this->returnCallback(
                    function() use ($iteratorData) {
                        return $iteratorData->position;
                    }
                )
            );

        $iteratorMock->expects($this->any())
            ->method('next')
            ->will(
                $this->returnCallback(
                    function() use ($iteratorData) {
                        $iteratorData->position++;
                    }
                )
            );

        $iteratorMock->expects($this->any())
            ->method('valid')
            ->will(
                $this->returnCallback(
                    function() use ($iteratorData) {
                        return isset($iteratorData->array[$iteratorData->position]);
                    }
                )
            );

        return $iteratorMock;
    }
}
