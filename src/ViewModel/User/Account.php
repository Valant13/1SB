<?php

namespace App\ViewModel\User;

use App\Entity\User\User;
use App\ViewModel\AbstractViewModel;
use Symfony\Component\HttpFoundation\Request;

class Account extends AbstractViewModel
{
    /**
     * @var string|null
     */
    private $nickname;

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->nickname = $request->request->get('nickname');
    }

    /**
     * @param User $user
     */
    public function fillFromUser(User $user): void
    {
        $this->nickname = $user->getNickname();
    }

    /**
     * @param User $user
     */
    public function fillUser(User $user): void
    {
        $user->setNickname($this->nickname);
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
}
