<?php

namespace App\Entity;

use App\Repository\SecretRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretRepository::class)]
class Secret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $secret = null;

    #[ORM\Column]
    private ?int $expireAfterViews = null;

    #[ORM\Column]
    private ?int $expireAfter = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getExpireAfterViews(): ?int
    {
        return $this->expireAfterViews;
    }

    public function setExpireAfterViews(int $expireAfterViews): self
    {
        $this->expireAfterViews = $expireAfterViews;

        return $this;
    }

    public function getExpireAfter(): ?int
    {
        return $this->expireAfter;
    }

    public function setExpireAfter(int $expireAfter): self
    {
        $this->expireAfter = $expireAfter;

        return $this;
    }
}
