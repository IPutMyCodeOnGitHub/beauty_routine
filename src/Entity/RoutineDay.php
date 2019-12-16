<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoutineDayRepository")
 */
class RoutineDay
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Routine", inversedBy="routineDays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $routine;

    /**
     * @ORM\Column(type="integer")
     */
    private $dayOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoutine(): ?Routine
    {
        return $this->routine;
    }

    public function setRoutine(?Routine $routine): self
    {
        $this->routine = $routine;

        return $this;
    }

    public function getDayOrder(): ?int
    {
        return $this->dayOrder;
    }

    public function setDayOrder(int $dayOrder): self
    {
        $this->dayOrder = $dayOrder;

        return $this;
    }
}
