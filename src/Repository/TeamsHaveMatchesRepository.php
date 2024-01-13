<?php

namespace App\Repository;

use App\Entity\SummerMatch;
use App\Entity\Team;
use App\Entity\TeamsHaveMatches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeamsHaveMatches>
 *
 * @method TeamsHaveMatches|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamsHaveMatches|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamsHaveMatches[]    findAll()
 * @method TeamsHaveMatches[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamsHaveMatchesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamsHaveMatches::class);
    }

    public function save(TeamsHaveMatches $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TeamsHaveMatches $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllGroupMatches (EntityManagerInterface $entityManager) :array
    {
        return $entityManager->createQueryBuilder()
            ->select('GROUP_CONCAT(thm.id) as IDs')
            ->addSelect(' GROUP_CONCAT(thm.goals) AS Score')
            ->addSelect(' GROUP_CONCAT(thm.nrPoints) AS Points')
            ->addSelect(' GROUP_CONCAT(t.name) AS Teams')
            ->addSelect('sm.startDate AS Date')
            ->from('App\Entity\TeamsHaveMatches', 'thm')
            ->leftJoin(Team::class,'t','WITH','t.id = thm.teamsHaveMatches ')
            ->join(SummerMatch::class,'sm', 'WITH', 'sm.id = thm.matchesHaveTeams')
            ->groupBy('thm.matchesHaveTeams')
            ->getQuery()
            ->getResult();
    }

}
