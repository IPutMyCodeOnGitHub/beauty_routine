<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RoutineUserDay", mappedBy="routineDay")
     */
    private $routineUserDays;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $recommends;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", inversedBy="routineDays")
     */
    private $products;

    public function __construct()
    {
        $this->routineUserDays = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

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
            $routineUserDay->setRoutineDay($this);
        }

        return $this;
    }

    public function removeRoutineUserDay(RoutineUserDay $routineUserDay): self
    {
        if ($this->routineUserDays->contains($routineUserDay)) {
            $this->routineUserDays->removeElement($routineUserDay);
            // set the owning side to null (unless already changed)
            if ($routineUserDay->getRoutineDay() === $this) {
                $routineUserDay->setRoutineDay(null);
            }
        }

        return $this;
    }

    public function getRecommends(): ?string
    {
        return $this->recommends;
    }

    public function setRecommends(?string $recommends): self
    {
        $this->recommends = $recommends;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }
}
