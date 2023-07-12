<?php

namespace App\Entity;

use App\Repository\RankingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingRepository::class)]
class Ranking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $maxPoints = null;

    #[ORM\OneToOne(inversedBy: 'ranking', cascade: ['persist', 'remove'])]
    private ?Team $team = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaxPoints(): ?int
    {
        return $this->maxPoints;
    }

    public function setMaxPoints(int $maxPoints): static
    {
        $this->maxPoints = $maxPoints;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }
}
