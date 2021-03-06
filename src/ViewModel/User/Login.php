<?php

namespace App\ViewModel\User;

use App\ViewModel\AbstractViewModel;
use Symfony\Component\HttpFoundation\Request;

class Login extends AbstractViewModel
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
        $this->nickname = (string)$request->request->get('nickname');
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
