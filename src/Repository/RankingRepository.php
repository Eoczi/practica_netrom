<?php

namespace App\Repository;

use App\Entity\Ranking;
use App\Entity\Team;
use App\Entity\TeamsHaveMatches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ranking>
 *
 * @method Ranking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ranking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ranking[]    findAll()
 * @method Ranking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RankingRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Ranking::class);
        $this->entityManager = $entityManager;

    }

    public function save(Ranking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ranking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getSortedResults(): array
    {
        return $this->findBy([],['maxPoints'=>'DESC']);
    }

    public function getGoals(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select(' IDENTITY(thm.teamsHaveMatches) AS thm_id', 't.name', 'SUM(thm.goals) AS total') //
            ->from('App\Entity\TeamsHaveMatches', 'thm')
            ->groupBy('thm_id')
            ->orderBy('total', 'DESC')
            ->innerJoin(Team::class,'t','WITH','t.id = IDENTITY(thm.teamsHaveMatches) ')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Ranking[] Returns an array of Ranking objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ranking
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
