<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RankingRepository;
use App\Service\RankingService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
require_once __DIR__ . '/../../vendor/autoload.php';

#[Route('/ranking')]
class RankingController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/', name: 'app_ranking_index', methods: ['GET'])]
    public function index(RankingRepository $rankingRepository, PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted ( attribute: "IS_AUTHENTICATED_FULLY");
        $user = $this->getUser();
        $userEntity = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        $rankingService = new RankingService($entityManager);
        $query = $rankingRepository->getSortedResults($userEntity);
        $rankingService->updateRankings();
        $rankings = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            8
        );
        return match ($user->isVerified()) {
            true => $this->render('ranking/index.html.twig', ['rankings' => $rankings,]),
            false => $this->render("security/verify_email.html.twig"),
        };
    }


    #[Route('/pdf', name: 'app_ranking_pdf')]
    public function generatePdf(RankingRepository $rankingRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userEntity = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        $html =  $this->renderView('ranking/table.html.twig', [
            'rankings' => $rankingRepository->getSortedResults($userEntity),
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
