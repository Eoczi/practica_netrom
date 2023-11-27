<?php

namespace App\Controller;

use App\Repository\RankingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ranking')]
class RankingController extends AbstractController
{
    #[Route('/', name: 'app_ranking_index', methods: ['GET'])]
    public function index(RankingRepository $rankingRepository): Response
    {
        return $this->render('ranking/index.html.twig', [
            'rankings' => $rankingRepository->getSortedResults(),
        ]);
    }
}
