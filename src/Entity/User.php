<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Asignatura::class, inversedBy: 'users')]
    private Collection $asignaturas;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Entrega::class, orphanRemoval: true)]
    private Collection $entregas;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Tema::class, orphanRemoval: true)]
    private Collection $temas;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Subida::class, orphanRemoval: true)]
    private Collection $subidas;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

//     /**
//  * @OneToMany(targetEntity="App\Entity\UserAsignatura", mappedBy="user", cascade={"persist", "remove"})
//  */
// private $userAsignaturas;

    public function __construct($id = null, $email = null, $password = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->asignaturas = new ArrayCollection();
        $this->entregas = new ArrayCollection();
        $this->temas = new ArrayCollection();
        // $this->userAsignaturas = new ArrayCollection();
        $this->subidas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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
     * @return Collection<int, Asignatura>
     */
    public function getAsignaturas(): Collection
    {
        return $this->asignaturas;
    }

    public function addAsignatura(Asignatura $asignatura): self
    {
        if (!$this->asignaturas->contains($asignatura)) {
            $this->asignaturas->add($asignatura);
        }

        return $this;
    }

    public function removeAsignatura(Asignatura $asignatura): self
    {
        $this->asignaturas->removeElement($asignatura);

        return $this;
    }

    /**
     * @return Collection<int, Entrega>
     */
    public function getEntregas(): Collection
    {
        return $this->entregas;
    }

    public function addEntrega(Entrega $entrega): self
    {
        if (!$this->entregas->contains($entrega)) {
            $this->entregas->add($entrega);
            $entrega->setUser($this);
        }

        return $this;
    }

    public function removeEntrega(Entrega $entrega): self
    {
        if ($this->entregas->removeElement($entrega)) {
            // set the owning side to null (unless already changed)
            if ($entrega->getUser() === $this) {
                $entrega->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tema>
     */
    public function getTemas(): Collection
    {
        return $this->temas;
    }

    public function addTema(Tema $tema): self
    {
        if (!$this->temas->contains($tema)) {
            $this->temas->add($tema);
            $tema->setUser($this);
        }

        return $this;
    }

    public function removeTema(Tema $tema): self
    {
        if ($this->temas->removeElement($tema)) {
            // set the owning side to null (unless already changed)
            if ($tema->getUser() === $this) {
                $tema->setUser(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection|UserAsignatura[]
    //  */
    // public function getUserAsignaturas(): Collection
    // {
    //     return $this->userAsignaturas;
    // }

    // public function addUserAsignatura(UserAsignatura $userAsignatura): self
    // {
    //     if (!$this->userAsignaturas->contains($userAsignatura)) {
    //         $this->userAsignaturas[] = $userAsignatura;
    //         $userAsignatura->setUser($this);
    //     }

    //     return $this;
    // }

    // public function removeUserAsignatura(UserAsignatura $userAsignatura): self
    // {
    //     if ($this->userAsignaturas->removeElement($userAsignatura)) {
    //         // set the owning side to null (unless already changed)
    //         if ($userAsignatura->getUser() === $this) {
    //             $userAsignatura->setUser(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Subida>
     */
    public function getSubidas(): Collection
    {
        return $this->subidas;
    }

    public function addSubida(Subida $subida): self
    {
        if (!$this->subidas->contains($subida)) {
            $this->subidas->add($subida);
            $subida->setUser($this);
        }

        return $this;
    }

    public function removeSubida(Subida $subida): self
    {
        if ($this->subidas->removeElement($subida)) {
            // set the owning side to null (unless already changed)
            if ($subida->getUser() === $this) {
                $subida->setUser(null);
            }
        }

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }
}
