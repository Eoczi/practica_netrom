<?php

namespace App\Controller;

use App\Repository\RankingRepository;
use App\Service\RankingService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ranking')]
class RankingController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/', name: 'app_ranking_index', methods: ['GET'])]
    public function index(RankingRepository $rankingRepository, RankingService $rankingService, EntityManagerInterface $entityManager): Response
    {
        $rankingService->updateRankings($entityManager);
        return $this->render('ranking/index.html.twig', [
            'rankings' => $rankingRepository->getSortedResults(),
        ]);
    }
}
