<?php

namespace App\Controller;

use App\Entity\TeamsHaveMatches;
use App\Entity\User;
use App\Form\TeamsHaveMatchesType;
use App\Repository\TeamsHaveMatchesRepository;
use App\Service\RankingService;
use App\Service\SummerMatchService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/matches')]
class TeamsHaveMatchesController extends AbstractController
{
    #[Route('/', name: 'app_teams_have_matches_index', methods: ['GET'])]
    public function index(TeamsHaveMatchesRepository $teamsHaveMatchesRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $this->denyAccessUnlessGranted ( attribute: "IS_AUTHENTICATED_FULLY");
        $user = $this->getUser();
        $userEntity = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        $query = $teamsHaveMatchesRepository->findAllGroupMatches($userEntity);
        $allGroupMatches = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return match ($user->isVerified()) {
            true => $this->render('teams_have_matches/index.html.twig', ['teams_have_matches' => $allGroupMatches,]),
            false => $this->render("security/verify_email.html.twig"),
        };
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/{id}/edit', name: 'app_teams_have_matches_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TeamsHaveMatches $teamsHaveMatch, TeamsHaveMatchesRepository $teamsHaveMatchesRepository, EntityManagerInterface $entityManager): Response
    {
        $summerMatchService = new SummerMatchService($entityManager);
        $rankingService = new RankingService($entityManager);
        //$user = $this->getUser();
        //$userEntity = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        $form = $this->createForm(TeamsHaveMatchesType::class, $teamsHaveMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teamsHaveMatchesRepository->save($teamsHaveMatch, true);
            $rankingService->updateRankings();
            $summerMatchService->updatePoints($teamsHaveMatch->getMatchesHaveTeams());

            return $this->redirectToRoute('app_teams_have_matches_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('teams_have_matches/edit.html.twig', [
            'teams_have_match' => $teamsHaveMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_teams_have_matches_delete', methods: ['POST'])]
    public function delete(Request $request, TeamsHaveMatches $teamsHaveMatch, TeamsHaveMatchesRepository $teamsHaveMatchesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$teamsHaveMatch->getId(), $request->request->get('_token'))) {
            $teamsHaveMatchesRepository->remove($teamsHaveMatch, true);
        }

        return $this->redirectToRoute('app_teams_have_matches_index', [], Response::HTTP_SEE_OTHER);
    }
}
