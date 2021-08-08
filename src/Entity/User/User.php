<?php

namespace App\Entity\User;

use App\Repository\User\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="user_entity")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $registrationIp;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registrationTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getRegistrationIp(): ?string
    {
        return $this->registrationIp;
    }

    public function setRegistrationIp(string $registrationIp): self
    {
        $this->registrationIp = $registrationIp;

        return $this;
    }

    public function getRegistrationTime(): ?\DateTimeInterface
    {
        return $this->registrationTime;
    }

    public function setRegistrationTime(\DateTimeInterface $registrationTime): self
    {
        $this->registrationTime = $registrationTime;

        return $this;
    }
}
