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
            $ranking = $rankingRepository->findOneBy(['team' => $teamID]);
            $ranking->setMaxPoints($totalPoints);
        }
        $this->entityManager->flush();
    }
}