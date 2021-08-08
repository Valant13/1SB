<?php

namespace App;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Auth
{
    const COOKIE_USER_NICKNAME_KEY = 'user_nickname';
    const SESSION_USER_ID_KEY = 'user_id';

    const USER_NICKNAME_MAX_LENGTH = 32;
    const USER_NICKNAME_MIN_LENGTH = 2;
    const USER_NICKNAME_PATTERN = '/\w+/';

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
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param RequestStack $requestStack
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        RequestStack $requestStack,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (!$this->isAuthorizedImpl()) {
            $this->tryAutologin();
        }

        if ($this->isAuthorizedImpl()) {
            $userId = $this->getUserIdFromSession();

            return $this->userRepository->find($userId);
        }

        return null;
    }

    /**
     * @return Response
     */
    public function getRedirectToLogin(): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('post_user_login'));
    }

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        if (!$this->isAuthorizedImpl()) {
            $this->tryAutologin();
        }

        return $this->isAuthorizedImpl();
    }

    /**
     * @param string $userNickname
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public function login(string $userNickname): void
    {
        if ($this->isAuthorizedImpl()) {
            throw new \BadMethodCallException('Already authorized');
        }

        $user = $this->userRepository->findOneBy(['nickname' => $userNickname]);

        if ($user === null) {
            throw new \InvalidArgumentException('User with this nickname does not exist');
        }

        $this->setUserIdToSession($user->getId());
        $this->setUserNicknameToCookie($user->getNickname());
    }

    /**
     *
     */
    public function logout(): void
    {
        $this->setUserIdToSession(null);
    }

    /**
     * @param string $userNickname
     * @param bool $isLoginNeeded
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public function register(string $userNickname, bool $isLoginNeeded = true): void
    {
        $this->validateUserNickname($userNickname);

        $user = new User();
        $user->setNickname($userNickname);
        $user->setRegistrationTime(new \DateTime());
        $user->setRegistrationIp($this->request->getClientIp());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($isLoginNeeded) {
            $this->login($userNickname);
        }
    }

    /**
     * @return bool
     */
    private function tryAutologin(): bool
    {
        $userNickname = $this->getUserNicknameFromCookie();

        if ($userNickname === null) {
            return false;
        }

        try {
            $this->login($userNickname);
        } finally {
            return $this->isAuthorized();
        }
    }

    /**
     * @return bool
     */
    private function isAuthorizedImpl(): bool
    {
        return $this->getUserIdFromSession() !== null;
    }

    /**
     * @param string $userNickname
     * @throws \InvalidArgumentException
     */
    private function validateUserNickname(string $userNickname): void
    {
        if (strlen($userNickname) > self::USER_NICKNAME_MAX_LENGTH) {
            throw new \InvalidArgumentException('Nickname is too long');
        }

        if (strlen($userNickname) < self::USER_NICKNAME_MIN_LENGTH) {
            throw new \InvalidArgumentException('Nickname is too short');
        }

        if (!preg_match(self::USER_NICKNAME_PATTERN, $userNickname)) {
            throw new \InvalidArgumentException('Nickname has prohibited chars');
        }

        $user = $this->userRepository->findOneBy(['nickname' => $userNickname]);

        if ($user !== null) {
            throw new \InvalidArgumentException('Nickname is already in use');
        }
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
     * @param int|null $userId
     */
    private function setUserIdToSession(?int $userId)
    {
        if ($userId === null) {
            $this->request->getSession()->remove(self::SESSION_USER_ID_KEY);
        } else {
            $this->request->getSession()->set(self::SESSION_USER_ID_KEY, $userId);
        }
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
     * @param string|null $userNickname
     */
    private function setUserNicknameToCookie(?string $userNickname)
    {
        if ($userNickname === null) {
            $this->request->cookies->remove(self::COOKIE_USER_NICKNAME_KEY);
        } else {
            $this->request->cookies->set(self::COOKIE_USER_NICKNAME_KEY, $userNickname);
        }
    }
}
