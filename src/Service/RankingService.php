<?php

namespace App\Service;

use App\Entity\Ranking;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RankingService
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function updateRankings(): void
    {
        $teamRepository = $this->entityManager->getRepository(Team::class);
        $rankingRepository = $this->entityManager->getRepository(Ranking::class);
        $teams = $teamRepository->findAll();

        foreach ($teams as $team) {
            $teamID = $team->getID();

            $totalPoints = (int) $this->entityManager->createQueryBuilder()
                ->select('SUM(thm.nrPoints) as totalPoints')
                ->from('App\Entity\TeamsHaveMatches', 'thm')
                ->where('thm.teamsHaveMatches = :teamID')
                ->setParameter('teamID', $teamID)
                ->getQuery()
                ->getSingleScalarResult();
            //is_null($totalPoints) ? : $totalPoints = 0;
            $ranking = $rankingRepository->findOneBy(['team' => $teamID]);
            $ranking->setMaxPoints($totalPoints);
            //$finalTotalPoints = 0;
            //is_null($totalPoints) ? : $finalTotalPoints = $totalPoints;

            /*if ($ranking) {
                $ranking->setMaxPoints($finalTotalPoints);
            } else {
                $ranking = new Ranking();
                $ranking->setTeam($team);
                $ranking->setMaxPoints($finalTotalPoints);
                $entityManager->persist($ranking);
            }*/
        }
        $this->entityManager->flush();
    }
}