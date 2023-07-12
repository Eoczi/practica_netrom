<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[ORM\Column]
    private ?int $nrPeople = null;

    #[ORM\OneToMany(mappedBy: 'teamsHaveMatches', targetEntity: TeamsHaveMatches::class)]
    private Collection $teamsHaveMatches;

    #[ORM\OneToMany(mappedBy: 'winner', targetEntity: SummerMatch::class)]
    private Collection $summerMatches;

    #[ORM\OneToOne(mappedBy: 'team', cascade: ['persist', 'remove'])]
    private ?Ranking $ranking = null;

    public function __construct()
    {
        $this->teamsHaveMatches = new ArrayCollection();
        $this->summerMatches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNrPeople(): ?int
    {
        return $this->nrPeople;
    }

    public function setNrPeople(int $nrPeople): static
    {
        $this->nrPeople = $nrPeople;

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
            $teamsHaveMatch->setTeamsHaveMatches($this);
        }

        return $this;
    }

    public function removeTeamsHaveMatch(TeamsHaveMatches $teamsHaveMatch): static
    {
        if ($this->teamsHaveMatches->removeElement($teamsHaveMatch)) {
            // set the owning side to null (unless already changed)
            if ($teamsHaveMatch->getTeamsHaveMatches() === $this) {
                $teamsHaveMatch->setTeamsHaveMatches(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SummerMatch>
     */
    public function getSummerMatches(): Collection
    {
        return $this->summerMatches;
    }

    public function addSummerMatch(SummerMatch $summerMatch): static
    {
        if (!$this->summerMatches->contains($summerMatch)) {
            $this->summerMatches->add($summerMatch);
            $summerMatch->setWinner($this);
        }

        return $this;
    }

    public function removeSummerMatch(SummerMatch $summerMatch): static
    {
        if ($this->summerMatches->removeElement($summerMatch)) {
            // set the owning side to null (unless already changed)
            if ($summerMatch->getWinner() === $this) {
                $summerMatch->setWinner(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getRanking(): ?Ranking
    {
        return $this->ranking;
    }

    public function setRanking(?Ranking $ranking): static
    {
        // unset the owning side of the relation if necessary
        if ($ranking === null && $this->ranking !== null) {
            $this->ranking->setTeam(null);
        }

        // set the owning side of the relation if necessary
        if ($ranking !== null && $ranking->getTeam() !== $this) {
            $ranking->setTeam($this);
        }

        $this->ranking = $ranking;

        return $this;
    }

}
