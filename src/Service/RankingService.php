<?php

namespace App\Service;

use App\Entity\Ranking;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RankingService
{
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function updateRankings(EntityManagerInterface $entityManager): void
    {
        $teamRepository = $entityManager->getRepository(Team::class);
        $rankingRepository = $entityManager->getRepository(Ranking::class);
        $teams = $teamRepository->findAll();

        foreach ($teams as $team) {
            $teamID = $team->getID();

            $totalPoints = $entityManager->createQueryBuilder()
                ->select('SUM(thm.nrPoints) as totalPoints')
                ->from('App\Entity\TeamsHaveMatches', 'thm')
                ->where('thm.teamsHaveMatches = :teamID')
                ->setParameter('teamID', $teamID)
                ->getQuery()
                ->getSingleScalarResult();

            $ranking = $rankingRepository->findOneBy(['team' => $teamID]);

            $finalTotalPoints = 0;
            is_null($totalPoints) ? : $finalTotalPoints = $totalPoints;
            if ($ranking) {
                $ranking->setMaxPoints($finalTotalPoints);
            } else {
                $ranking = new Ranking();
                $ranking->setTeam($team);
                $ranking->setMaxPoints($finalTotalPoints);
                $entityManager->persist($ranking);
            }
        }
        $entityManager->flush();
    }
}