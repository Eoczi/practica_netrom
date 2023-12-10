<?php

namespace App\Entity;

use App\Repository\SummerMatchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SummerMatchRepository::class)]
class SummerMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\GreaterThanOrEqual(
        value: new \DateTimeImmutable('now'),
        message: 'The start date must be later than or equal to the current date.'
    )]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\OneToMany(mappedBy: 'matchesHaveTeams', targetEntity: TeamsHaveMatches::class, cascade: ['persist', 'remove'])]
    private Collection $teamsHaveMatches;

    #[ORM\ManyToOne(inversedBy: 'summerMatches')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Team $winner = null;

    public function __construct()
    {
        $this->teamsHaveMatches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return Collection<int, TeamsHaveMatches>
     */
    public function getTeamsHaveMatches(): Collection
    {
        return $this->teamsHaveMatches;
    }

    public function addTeamsHaveMatch(TeamsHaveMatches $teamsHaveMatch): static
    {
        if (!$this->teamsHaveMatches->contains($teamsHaveMatch)) {
            $this->teamsHaveMatches->add($teamsHaveMatch);
            $teamsHaveMatch->setMatchesHaveTeams($this);
        }

        return $this;
    }

    public function removeTeamsHaveMatch(TeamsHaveMatches $teamsHaveMatch): static
    {
        if ($this->teamsHaveMatches->removeElement($teamsHaveMatch)) {
            // set the owning side to null (unless already changed)
            if ($teamsHaveMatch->getMatchesHaveTeams() === $this) {
                $teamsHaveMatch->setMatchesHaveTeams(null);
            }
        }

        return $this;
    }

    public function getWinner(): ?Team
    {
        return $this->winner;
    }

    public function setWinner(?Team $winner): static
    {
        $this->winner = $winner;

        return $this;
    }
    public function __toString(): string
    {
        return $this->getWinner() ? (string) $this->getWinner() : '';
    }
}
