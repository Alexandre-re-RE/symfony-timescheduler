<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $end_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $real_start_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $real_end_date = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeImmutable $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeImmutable $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getRealStartDate(): ?\DateTimeImmutable
    {
        return $this->real_start_date;
    }

    public function setRealStartDate(?\DateTimeImmutable $real_start_date): self
    {
        $this->real_start_date = $real_start_date;

        return $this;
    }

    public function getRealEndDate(): ?\DateTimeImmutable
    {
        return $this->real_end_date;
    }

    public function setRealEndDate(?\DateTimeImmutable $real_end_date): self
    {
        $this->real_end_date = $real_end_date;

        return $this;
    }
}
