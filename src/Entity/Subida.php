<?php

namespace App\Entity;

use App\Repository\SubidaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubidaRepository::class)]
class Subida
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_subida = null;

    #[ORM\ManyToOne(inversedBy: 'subidas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entrega $entrega = null;

    #[ORM\ManyToOne(inversedBy: 'subidas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?float $nota = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFechaSubida(): ?\DateTimeInterface
    {
        return $this->fecha_subida;
    }

    public function setFechaSubida(?\DateTimeInterface $fecha_subida): self
    {
        $this->fecha_subida = $fecha_subida;

        return $this;
    }

    public function getEntrega(): ?Entrega
    {
        return $this->entrega;
    }

    public function setEntrega(?Entrega $entrega): self
    {
        $this->entrega = $entrega;

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

    public function getNota(): ?float
    {
        return $this->nota;
    }

    public function setNota(?float $nota): self
    {
        $this->nota = $nota;

        return $this;
    }
}
