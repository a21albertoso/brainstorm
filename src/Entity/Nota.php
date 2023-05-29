<?php

namespace App\Entity;

use App\Repository\NotaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotaRepository::class)]
class Nota
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $numero = null;

    #[ORM\OneToOne(targetEntity: Subida::class, inversedBy: 'nota')]
    #[ORM\JoinColumn(nullable: false)]
    private $subida;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?float
    {
        return $this->numero;
    }

    public function setNumero(?float $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getSubida(): ?Subida
    {
        return $this->subida;
    }

    public function setSubida(?Subida $subida): self
    {
        $this->subida = $subida;

        return $this;
    }
}
