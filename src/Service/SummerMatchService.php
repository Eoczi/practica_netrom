<?php

namespace App\Service;

use App\Entity\SummerMatch;
use App\Entity\TeamsHaveMatches;
use Doctrine\ORM\EntityManagerInterface;

class SummerMatchService
{
    public function updatePoints(EntityManagerInterface $entityManager,SummerMatch $summerMatch): void
    {
        $teamsHaveMatchesRepository = $entityManager->getRepository(TeamsHaveMatches::class);
        $summerMatchID = $summerMatch->getId();
        $matches= $teamsHaveMatchesRepository->findBy(['matchesHaveTeams' => $summerMatchID]);
        foreach($matches as $match){
            if ($summerMatch->getWinner()->getName() == $match->getTeamsHaveMatches()->getName())
                $match->setNrPoints(3);
            else
                $match->setNrPoints(0);
        }
        $entityManager->flush();
    }
}