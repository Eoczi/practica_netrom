<?php

namespace App\Entity;

use App\Repository\TeamsHaveMatchesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamsHaveMatchesRepository::class)]
class TeamsHaveMatches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\AtLeastOneOf([
        new Assert\EqualTo(3),
        new Assert\EqualTo(1),
        new Assert\EqualTo(0),
    ])]
    #[ORM\Column]
    private ?int $nrPoints = null;

    #[ORM\ManyToOne(inversedBy: 'teamsHaveMatches')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Team $teamsHaveMatches = null;

    #[ORM\ManyToOne(inversedBy: 'teamsHaveMatches')]
    private ?SummerMatch $matchesHaveTeams = null;

    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true)]
    private ?int $goals = null;

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

    public function getGoals(): ?int
    {
        return $this->goals;
    }

    public function setGoals(?int $goals): static
    {
        $this->goals = $goals;

        return $this;
    }

}
