<?php

namespace App;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class Auth
{
    const COOKIE_USER_NICKNAME_KEY = 'user_nickname';
    const SESSION_USER_ID_KEY = 'user_id';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        $userId = $this->getUserIdFromSession();

        if ($userId !== null) {
            return $this->userRepository->find($userId);
        }

        $userNickname = $this->getUserNicknameFromCookie();

        $user = $this->userRepository->findOneBy(['nickname' => $userNickname]);
        if ($user !== null) {
            $this->setUserIdToSession($user->getId());

            return $user;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->getUser() !== null;
    }

    /**
     * @param string $userNickname
     */
    public function login(string $userNickname)
    {
        $existingUser = $this->userRepository->findOneBy(['nickname' => $userNickname]);

        if ($existingUser !== null) {
            $this->setUserIdToSession($existingUser->getId());
            $this->setUserNicknameToCookie($existingUser->getNickname());
        } else {
            $newUser = new User();
            $newUser->setNickname($userNickname);
            $newUser->setRegistrationTime(new \DateTime());
            $newUser->setRegistrationIp($this->request->getClientIp());

            $this->entityManager->persist($newUser);
            $this->entityManager->flush();

            $this->setUserIdToSession($newUser->getId());
            $this->setUserNicknameToCookie($newUser->getNickname());
        }
    }

    /**
     *
     */
    public function logout()
    {
        $this->removeUserIdFromSession();
        $this->removeUserNicknameFromCookie();
    }

    public function getRedirectToLogin()
    {
        // TODO: implement
    }

    /**
     * @return int|null
     */
    private function getUserIdFromSession(): ?int
    {
        $userIdString = $this->request->getSession()->get(self::SESSION_USER_ID_KEY);

        return is_numeric($userIdString) ? (int)$userIdString : null;
    }

    /**
     * @param int $userId
     */
    private function setUserIdToSession(int $userId)
    {
        $this->request->getSession()->set(self::SESSION_USER_ID_KEY, $userId);
    }

    /**
     *
     */
    private function removeUserIdFromSession()
    {
        $this->request->getSession()->remove(self::SESSION_USER_ID_KEY);
    }

    /**
     * @return string|null
     */
    private function getUserNicknameFromCookie(): ?string
    {
        if ($this->request->cookies->has(self::COOKIE_USER_NICKNAME_KEY)) {
            return $this->request->cookies->get(self::COOKIE_USER_NICKNAME_KEY);
        } else {
            return null;
        }
    }

    /**
     * @param string $userNickname
     */
    private function setUserNicknameToCookie(string $userNickname)
    {
        $this->request->cookies->set(self::COOKIE_USER_NICKNAME_KEY, $userNickname);
    }

    /**
     *
     */
    private function removeUserNicknameFromCookie()
    {
        $this->request->cookies->remove(self::COOKIE_USER_NICKNAME_KEY);
    }
}
