<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoutineUserDayRepository")
 */
class RoutineUserDay
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RoutineSelection", inversedBy="routineUserDays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $routineSelection;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RoutineDay", inversedBy="routineUserDays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $routineDay;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_completed;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateCompleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_changed;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", inversedBy="routineUserDays")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoutineSelection(): ?RoutineSelection
    {
        return $this->routineSelection;
    }

    public function setRoutineSelection(?RoutineSelection $routineSelection): self
    {
        $this->routineSelection = $routineSelection;

        return $this;
    }

    public function getRoutineDay(): ?RoutineDay
    {
        return $this->routineDay;
    }

    public function setRoutineDay(?RoutineDay $routineDay): self
    {
        $this->routineDay = $routineDay;

        return $this;
    }

    public function getIsCompleted(): ?bool
    {
        return $this->is_completed;
    }

    public function setIsCompleted(?bool $is_completed): self
    {
        $this->is_completed = $is_completed;

        return $this;
    }

    public function getDateCompleted(): ?\DateTimeInterface
    {
        return $this->dateCompleted;
    }

    public function setDateCompleted(\DateTimeInterface $dateCompleted): self
    {
        $this->dateCompleted = $dateCompleted;

        return $this;
    }

    public function getIsChanged(): ?bool
    {
        return $this->is_changed;
    }

    public function setIsChanged(?bool $is_changed): self
    {
        $this->is_changed = $is_changed;

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
