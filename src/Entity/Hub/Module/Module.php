<?php

namespace App\Entity\Hub\Module;

use App\Entity\Hub\Hub;
use App\Repository\Hub\Module\ModuleRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'hub_module')]
class Module {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Name should not be blank.')]
    #[Assert\Length(min: 3, max: 100, minMessage: 'Name should be at least {{ limit }} characters', maxMessage: 'Name should not be more than {{ limit }} characters')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s]+$/', message: 'Name should not contain any special characters')]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Type should not be blank.')]
    #[Assert\Choice(choices: ['environment', 'switch'], message: 'Type should be either environment or switch')]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Data::class, orphanRemoval: true)]
    private Collection $data;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Action::class, orphanRemoval: true)]
    private Collection $actions;

    #[ORM\Column(nullable: true)]
    private ?int $batteryLevel = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $pingAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hub $hub = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Mac address should not be blank.')]
    #[Assert\Length(min: 17, max: 17, minMessage: 'Mac address should be at least {{ limit }} characters', maxMessage: 'Mac address should not be more than {{ limit }} characters')]
    #[Assert\Regex(pattern: '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', message: 'Mac address should be in the format of XX:XX:XX:XX:XX:XX')]
    private ?string $macAddress = null;

    public function __construct() {
        $this->data = new ArrayCollection();
        $this->actions = new ArrayCollection();
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

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(string $type): static {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Data>
     */
    public function getData(): Collection {
        return $this->data;
    }

    public function addData(Data $data): static {
        if (!$this->data->contains($data)) {
            $this->data->add($data);
            $data->setModule($this);
        }

        return $this;
    }

    public function removeData(Data $data): static {
        if ($this->data->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getModule() === $this) {
                $data->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection {
        return $this->actions;
    }

    public function addAction(Action $action): static {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setModule($this);
        }

        return $this;
    }

    public function removeAction(Action $action): static {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getModule() === $this) {
                $action->setModule(null);
            }
        }

        return $this;
    }

    public function getBatteryLevel(): ?int {
        return $this->batteryLevel;
    }

    public function setBatteryLevel(?int $batteryLevel): static {
        $this->batteryLevel = $batteryLevel;

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

    public function getHub(): ?Hub {
        return $this->hub;
    }

    public function setHub(?Hub $hub): static {
        $this->hub = $hub;

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

    public function getMacAddress(): ?string
    {
        return $this->macAddress;
    }

    public function setMacAddress(?string $macAddress): static
    {
        $this->macAddress = $macAddress;

        return $this;
    }
}
