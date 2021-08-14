<?php

namespace App;

use App\Entity\User\User;
use App\Entity\User\UserLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Logger
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Auth $auth
     * @param RequestStack $requestStack
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Auth $auth,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->auth = $auth;
        $this->requestStack = $requestStack;
    }

    /**
     * @param User|null $user
     * @param Request|null $request
     */
    public function logUserRequest(User $user = null, Request $request = null): void
    {
        if ($user === null) {
            if (!$this->auth->isAuthorized()) {
                throw new \BadMethodCallException('User is not authorized');
            }

            $user = $this->auth->getUser();
        }

        if ($request === null) {
            $request = $this->requestStack->getCurrentRequest();
        }

        $record = new UserLog();

        $record->setUser($user);
        $record->setUserNickname($user->getNickname());
        $record->setRequestUrl($request->getMethod() . ' ' . $request->getRequestUri());
        $record->setRequestIp($request->getClientIp());
        $record->setRequestTime(new \DateTime());

        $this->entityManager->persist($record);
        $this->entityManager->flush();
    }
}
