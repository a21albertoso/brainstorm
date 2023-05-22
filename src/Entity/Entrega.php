<?php

namespace App\Entity;

use App\Repository\EntregaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntregaRepository::class)]
class Entrega
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'entregas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Asignatura $asignatura = null;

    #[ORM\ManyToOne(inversedBy: 'entregas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_entrega = null;

    #[ORM\OneToMany(mappedBy: 'entrega', targetEntity: Subida::class, orphanRemoval: true)]
    private Collection $subidas;

    public function __construct()
    {
        $this->subidas = new ArrayCollection();
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

    public function getAsignatura(): ?Asignatura
    {
        return $this->asignatura;
    }

    public function setAsignatura(?Asignatura $asignatura): self
    {
        $this->asignatura = $asignatura;

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

    public function getFechaEntrega(): ?\DateTimeInterface
    {
        return $this->fecha_entrega;
    }

    public function setFechaEntrega(\DateTimeInterface $fecha_entrega): self
    {
        $this->fecha_entrega = $fecha_entrega;

        return $this;
    }

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
            $subida->setEntrega($this);
        }

        return $this;
    }

    public function removeSubida(Subida $subida): self
    {
        if ($this->subidas->removeElement($subida)) {
            // set the owning side to null (unless already changed)
            if ($subida->getEntrega() === $this) {
                $subida->setEntrega(null);
            }
        }

        return $this;
    }
}
