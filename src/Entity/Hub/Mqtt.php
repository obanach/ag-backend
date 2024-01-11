<?php

namespace App\Entity\Hub;

use App\Repository\Hub\MqttRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MqttRepository::class)]
#[ORM\Table(name: 'hub_mqtt')]
#[ORM\HasLifecycleCallbacks]
class Mqtt {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $username = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Choice(choices: ['publish', 'subscribe', 'all'])]
    private ?string $action = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Choice(choices: ['allow', 'deny'])]
    private ?string $permission = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $topic = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(string $username): static {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): static {
        $this->password = $password;

        return $this;
    }

    public function getAction(): ?string {
        return $this->action;
    }

    public function setAction(string $action): static {
        $this->action = $action;

        return $this;
    }

    public function getPermission(): ?string {
        return $this->permission;
    }

    public function setPermission(string $permission): static {
        $this->permission = $permission;

        return $this;
    }

    public function getTopic(): ?string {
        return $this->topic;
    }

    public function setTopic(string $topic): static {
        $this->topic = $topic;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValues(): void {
        $this->createdAt = new DateTimeImmutable();
    }
}
