<?php

namespace App\Service;

use App\Entity\SummerMatch;
use App\Entity\TeamsHaveMatches;
use Doctrine\ORM\EntityManagerInterface;

class SummerMatchService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    public function updatePoints(SummerMatch $summerMatch): void
    {
        $teamsHaveMatchesRepository = $this->entityManager->getRepository(TeamsHaveMatches::class);
        $summerMatchID = $summerMatch->getId();
        $matches= $teamsHaveMatchesRepository->findBy(['matchesHaveTeams' => $summerMatchID]);
        if ($matches[0]->getGoals() > $matches[1]->getGoals())
        {
            $matches[0]->setNrPoints(3);
            $matches[1]->setNrPoints(0);
            $summerMatch->setWinner($matches[0]->getTeamsHaveMatches());
        }
        elseif($matches[0]->getGoals() < $matches[1]->getGoals()) {
            $matches[0]->setNrPoints(0);
            $matches[1]->setNrPoints(3);
            $summerMatch->setWinner($matches[1]->getTeamsHaveMatches());
        }
        else {
            $matches[0]->setNrPoints(1);
            $matches[1]->setNrPoints(1);
            $summerMatch->setWinner(null);
        }

        $this->entityManager->flush();
    }
}