<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoutineSelectionRepository")
 */
class RoutineSelection
{
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_UNSUB = 'unsubscribe';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Routine", inversedBy="routineSelections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parentRoutine;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastCompletedDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $daysCompleted;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="routineSelections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RoutineUserDay", mappedBy="routineSelection", orphanRemoval=true)
     */
    private $routineUserDays;

    public function __construct()
    {
        $this->routineUserDays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentRoutine(): ?Routine
    {
        return $this->parentRoutine;
    }

    public function setParentRoutine(?Routine $parentRoutine): self
    {
        $this->parentRoutine = $parentRoutine;

        return $this;
    }

    public function getLastCompletedDate(): ?\DateTimeInterface
    {
        return $this->lastCompletedDate;
    }

    public function setLastCompletedDate(?\DateTimeInterface $lastCompletedDate): self
    {
        $this->lastCompletedDate = $lastCompletedDate;

        return $this;
    }

    public function getDaysCompleted(): ?int
    {
        return $this->daysCompleted;
    }

    public function setDaysCompleted(?int $daysCompleted): self
    {
        $this->daysCompleted = $daysCompleted;

        return $this;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|RoutineUserDay[]
     */
    public function getRoutineUserDays(): Collection
    {
        return $this->routineUserDays;
    }

    public function addRoutineUserDay(RoutineUserDay $routineUserDay): self
    {
        if (!$this->routineUserDays->contains($routineUserDay)) {
            $this->routineUserDays[] = $routineUserDay;
            $routineUserDay->setRoutineSelection($this);
        }

        return $this;
    }

    public function removeRoutineUserDay(RoutineUserDay $routineUserDay): self
    {
        if ($this->routineUserDays->contains($routineUserDay)) {
            $this->routineUserDays->removeElement($routineUserDay);
            // set the owning side to null (unless already changed)
            if ($routineUserDay->getRoutineSelection() === $this) {
                $routineUserDay->setRoutineSelection(null);
            }
        }

        return $this;
    }
}
