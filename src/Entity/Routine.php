<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoutineRepository")
 */
class Routine
{
    const STATUS_DRAFT = 'draft';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLED = 'disabled';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="routines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cycle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RoutineType", inversedBy="routines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OrderBy({"dayOrder" = "ASC"})
     * @ORM\OneToMany(targetEntity="App\Entity\RoutineDay", mappedBy="routine", orphanRemoval=true)
     */
    private $routineDays;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="ыгsubs")
     */
    private $subscriber;

    public function __construct()
    {
        $this->routineDays = new ArrayCollection();
        $this->subscriber = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCycle(): ?int
    {
        return $this->cycle;
    }

    public function setCycle(?int $cycle): self
    {
        $this->cycle = $cycle;

        return $this;
    }

    public function getType(): ?RoutineType
    {
        return $this->type;
    }

    public function setType(?RoutineType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|RoutineDay[]
     */
    public function getRoutineDays(): Collection
    {
        return $this->routineDays;
    }

    public function addRoutineDay(RoutineDay $routineDay): self
    {
        if (!$this->routineDays->contains($routineDay)) {
            $this->routineDays[] = $routineDay;
            $routineDay->setRoutine($this);
        }

        return $this;
    }

    public function removeRoutineDay(RoutineDay $routineDay): self
    {
        if ($this->routineDays->contains($routineDay)) {
            $this->routineDays->removeElement($routineDay);
            // set the owning side to null (unless already changed)
            if ($routineDay->getRoutine() === $this) {
                $routineDay->setRoutine(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, array(self::STATUS_ACTIVE, self::STATUS_BLOCKED, self::STATUS_DISABLED, self::STATUS_DRAFT))) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSubscriber(): Collection
    {
        return $this->subscriber;
    }

    public function addSubscriber(User $subscriber): self
    {
        if (!$this->subscriber->contains($subscriber)) {
            $this->subscriber[] = $subscriber;
        }

        return $this;
    }

    public function removeSubscriber(User $subscriber): self
    {
        if ($this->subscriber->contains($subscriber)) {
            $this->subscriber->removeElement($subscriber);
        }

        return $this;
    }
}
