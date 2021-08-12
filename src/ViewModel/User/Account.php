<?php

namespace App\ViewModel\User;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\UserInterest;
use App\Entity\User\User;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Grid\Grid;
use App\ViewModel\User\Account\InterestDeviceGrid;
use App\ViewModel\User\Account\InterestMaterialGrid;
use Symfony\Component\HttpFoundation\Request;

class Account extends AbstractViewModel
{
    /**
     * @var string|null
     */
    private $nickname;

    /**
     * @var Grid
     */
    private $interestMaterialGrid;

    /**
     * @var Grid
     */
    private $interestDeviceGrid;

    /**
     * @param Material[] $materials
     * @param Device[] $devices
     */
    public function __construct(array $materials, array $devices)
    {
        $this->interestMaterialGrid = new Grid(
            'interest-material-grid',
            new InterestMaterialGrid(),
            $materials
        );

        $this->interestDeviceGrid = new Grid(
            'interest-device-grid',
            new InterestDeviceGrid(),
            $devices
        );
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->nickname = $request->request->get('nickname');

        $this->interestMaterialGrid->fillFromRequest($request);
        $this->interestDeviceGrid->fillFromRequest($request);
    }

    /**
     * @param User $user
     * @param UserInterest $userInterest
     */
    public function fillFromUser(User $user, UserInterest $userInterest): void
    {
        $this->nickname = $user->getNickname();

        $this->interestMaterialGrid->fillFromModels($userInterest->getMaterials()->toArray());
        $this->interestDeviceGrid->fillFromModels($userInterest->getDevices()->toArray());
    }

    /**
     * @param User $user
     * @param UserInterest $userInterest
     */
    public function fillUser(User $user, UserInterest $userInterest): void
    {
        $user->setNickname($this->nickname);

        $this->interestMaterialGrid->fillModels($userInterest->getMaterials()->toArray(), $userInterest);
        $this->interestDeviceGrid->fillModels($userInterest->getDevices()->toArray(), $userInterest);
    }

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string|null $nickname
     */
    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return Grid
     */
    public function getInterestMaterialGrid(): Grid
    {
        return $this->interestMaterialGrid;
    }

    /**
     * @param Grid $interestMaterialGrid
     */
    public function setInterestMaterialGrid(Grid $interestMaterialGrid): void
    {
        $this->interestMaterialGrid = $interestMaterialGrid;
    }

    /**
     * @return Grid
     */
    public function getInterestDeviceGrid(): Grid
    {
        return $this->interestDeviceGrid;
    }

    /**
     * @param Grid $interestDeviceGrid
     */
    public function setInterestDeviceGrid(Grid $interestDeviceGrid): void
    {
        $this->interestDeviceGrid = $interestDeviceGrid;
    }
}
