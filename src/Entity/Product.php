<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductType", inversedBy="tag")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductTag", inversedBy="products")
     */
    private $tag;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\RoutineDay", mappedBy="products")
     */
    private $routineDays;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\RoutineUserDay", mappedBy="products")
     */
    private $routineUserDays;

    public function __construct()
    {
        $this->type = new ArrayCollection();
        $this->tag = new ArrayCollection();
        $this->routineDays = new ArrayCollection();
        $this->routineUserDays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|ProductType[]
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(ProductType $type): self
    {
        if (!$this->type->contains($type)) {
            $this->type[] = $type;
        }

        return $this;
    }

    public function removeType(ProductType $type): self
    {
        if ($this->type->contains($type)) {
            $this->type->removeElement($type);
        }

        return $this;
    }

    /**
     * @return Collection|ProductTag[]
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(ProductTag $tag): self
    {
        if (!$this->tag->contains($tag)) {
            $this->tag[] = $tag;
        }

        return $this;
    }

    public function removeTag(ProductTag $tag): self
    {
        if ($this->tag->contains($tag)) {
            $this->tag->removeElement($tag);
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

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
            $routineDay->addProduct($this);
        }

        return $this;
    }

    public function removeRoutineDay(RoutineDay $routineDay): self
    {
        if ($this->routineDays->contains($routineDay)) {
            $this->routineDays->removeElement($routineDay);
            $routineDay->removeProduct($this);
        }

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
            $routineUserDay->addProduct($this);
        }

        return $this;
    }

    public function removeRoutineUserDay(RoutineUserDay $routineUserDay): self
    {
        if ($this->routineUserDays->contains($routineUserDay)) {
            $this->routineUserDays->removeElement($routineUserDay);
            $routineUserDay->removeProduct($this);
        }

        return $this;
    }
}
