<?php

namespace App\Entity;

use App\Services\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_EXPERT = 'ROLE_EXPERT';
    const ROLE_INVALID_EXPERT = 'ROLE_INVALID_EXPERT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserCertificate", mappedBy="user")
     */
    private $userCertificates;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verifyCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Routine", mappedBy="user", orphanRemoval=true)
     */
    private $routines;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Routine", mappedBy="subscriber")
     */
    private $subs;

    public function __construct()
    {
        $this->userCertificates = new ArrayCollection();
        $this->routines = new ArrayCollection();
        $this->subs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        $this->roles = array_unique($roles);
        return$this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|UserCertificate[]
     */
    public function getUserCertificates(): Collection
    {
        return $this->userCertificates;
    }

    public function addUserCertificate(UserCertificate $userCertificate): self
    {
        if (!$this->userCertificates->contains($userCertificate)) {
            $this->userCertificates[] = $userCertificate;
            $userCertificate->setUser($this);
        }

        return $this;
    }

    public function removeUserCertificate(UserCertificate $userCertificate): self
    {
        if ($this->userCertificates->contains($userCertificate)) {
            $this->userCertificates->removeElement($userCertificate);
            // set the owning side to null (unless already changed)
            if ($userCertificate->getUser() === $this) {
                $userCertificate->setUser(null);
            }
        }

        return $this;
    }


    public function getVerifyCode(): ?string
    {
        return $this->verifyCode;
    }

    public function setVerifyCode(?string $verifyCode): self
    {
        $this->verifyCode = $verifyCode;
        return $this;
    }

    /**
     * @return Collection|Routine[]
     */
    public function getRoutines(): Collection
    {
        return $this->routines;
    }

    public function addRoutine(Routine $routine): self
    {
        if (!$this->routines->contains($routine)) {
            $this->routines[] = $routine;
            $routine->setUser($this);
        }

        return $this;
    }

    public function removeRoutine(Routine $routine): self
    {
        if ($this->routines->contains($routine)) {
            $this->routines->removeElement($routine);
            // set the owning side to null (unless already changed)
            if ($routine->getUser() === $this) {
                $routine->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Routine[]
     */
    public function getSubs(): Collection
    {
        return $this->subs;
    }

    public function addSub(Routine $sub): self
    {
        if (!$this->subs->contains($sub)) {
            $this->subs[] = $sub;
            $sub->addSubscriber($this);
        }

        return $this;
    }

    public function removeSub(Routine $sub): self
    {
        if ($this->subs->contains($sub)) {
            $this->subs->removeElement($sub);
            $sub->removeSubscriber($this);
        }
    }

}
