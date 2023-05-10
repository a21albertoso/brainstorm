<?php

namespace App\Entity;

use App\Repository\AsignaturaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsignaturaRepository::class)]
class Asignatura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\OneToMany(mappedBy: 'asignatura', targetEntity: Entrega::class, orphanRemoval: true)]
    private Collection $entregas;

    #[ORM\OneToMany(mappedBy: 'asignatura', targetEntity: Tema::class, orphanRemoval: true)]
    private Collection $temas;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'asignaturas')]
    private Collection $users;

    public function __construct()
    {
        $this->entregas = new ArrayCollection();
        $this->temas = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

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
            $entrega->setAsignatura($this);
        }

        return $this;
    }

    public function removeEntrega(Entrega $entrega): self
    {
        if ($this->entregas->removeElement($entrega)) {
            // set the owning side to null (unless already changed)
            if ($entrega->getAsignatura() === $this) {
                $entrega->setAsignatura(null);
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
            $tema->setAsignatura($this);
        }

        return $this;
    }

    public function removeTema(Tema $tema): self
    {
        if ($this->temas->removeElement($tema)) {
            // set the owning side to null (unless already changed)
            if ($tema->getAsignatura() === $this) {
                $tema->setAsignatura(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addAsignatura($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeAsignatura($this);
        }

        return $this;
    }
}
