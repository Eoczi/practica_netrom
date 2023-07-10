<?php

namespace App\Entity;

use App\Repository\TeamsHaveMatchesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamsHaveMatchesRepository::class)]
class TeamsHaveMatches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nrPoints = null;

    #[ORM\ManyToOne(inversedBy: 'teamsHaveMatches')]
    private ?Team $teamsHaveMatches = null;

    #[ORM\ManyToOne(inversedBy: 'teamsHaveMatches')]
    private ?SummerMatch $matchesHaveTeams = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNrPoints(): ?int
    {
        return $this->nrPoints;
    }

    public function setNrPoints(int $nrPoints): static
    {
        $this->nrPoints = $nrPoints;

        return $this;
    }

    public function getTeamsHaveMatches(): ?Team
    {
        return $this->teamsHaveMatches;
    }

    public function setTeamsHaveMatches(?Team $teamsHaveMatches): static
    {
        $this->teamsHaveMatches = $teamsHaveMatches;

        return $this;
    }

    public function getMatchesHaveTeams(): ?SummerMatch
    {
        return $this->matchesHaveTeams;
    }

    public function setMatchesHaveTeams(?SummerMatch $matchesHaveTeams): static
    {
        $this->matchesHaveTeams = $matchesHaveTeams;

        return $this;
    }
}
