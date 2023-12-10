<?php

namespace App\Controller;

use App\Repository\RankingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
require_once __DIR__ . '/../../vendor/autoload.php';

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


    #[Route('/pdf', name: 'app_ranking_pdf')]
    public function generatePdf(RankingRepository $rankingRepository): Response
    {

        $html =  $this->renderView('ranking/table.html.twig', [
            'rankings' => $rankingRepository->getSortedResults(),
            'teamsGoals' => $rankingRepository->getGoals(),
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="ranking.pdf"',
        ]);
    }
}
