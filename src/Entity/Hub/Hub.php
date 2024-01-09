<?php

namespace App\Entity\Hub;

use App\Entity\Hub\Module\Module;
use App\Entity\User;
use App\Repository\Hub\HubRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HubRepository::class)]
#[ORM\Table(name: 'hub')]
#[ORM\HasLifecycleCallbacks]
class Hub {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $pairCode = null;

    #[ORM\OneToMany(mappedBy: 'hub', targetEntity: Module::class, orphanRemoval: true)]
    private Collection $modules;

    #[ORM\OneToMany(mappedBy: 'hub', targetEntity: Log::class, orphanRemoval: true)]
    private Collection $logs;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $mqttName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $mqttKey = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $pingAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(inversedBy: 'hubs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct() {
        $this->modules = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getPairCode(): ?int {
        return $this->pairCode;
    }

    public function setPairCode(?int $pairCode): static {
        $this->pairCode = $pairCode;

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection {
        return $this->modules;
    }

    public function addModule(Module $module): static {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setHub($this);
        }

        return $this;
    }

    public function removeModule(Module $module): static {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getHub() === $this) {
                $module->setHub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Log>
     */
    public function getLogs(): Collection {
        return $this->logs;
    }

    public function addLog(Log $log): static {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setHub($this);
        }

        return $this;
    }

    public function removeLog(Log $log): static {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getHub() === $this) {
                $log->setHub(null);
            }
        }

        return $this;
    }

    public function getAccessToken(): ?string {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): static {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getMqttName(): ?string {
        return $this->mqttName;
    }

    public function setMqttName(string $mqttName): static {
        $this->mqttName = $mqttName;

        return $this;
    }

    public function getMqttKey(): ?string {
        return $this->mqttKey;
    }

    public function setMqttKey(string $mqttKey): static {
        $this->mqttKey = $mqttKey;

        return $this;
    }

    public function getPingAt(): ?DateTimeImmutable {
        return $this->pingAt;
    }

    public function setPingAt(?DateTimeImmutable $pingAt): static {
        $this->pingAt = $pingAt;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeImmutable {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): static {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValues(): void {
        $this->createdAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValues(): void {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
