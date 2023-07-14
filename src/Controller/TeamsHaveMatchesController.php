<?php

namespace App\Controller;

use App\Entity\Ranking;
use App\Entity\Team;
use App\Entity\TeamsHaveMatches;
use App\Form\TeamsHaveMatchesType;
use App\Repository\TeamsHaveMatchesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teams/have/matches')]
class TeamsHaveMatchesController extends AbstractController
{

    #[Route('/', name: 'app_teams_have_matches_index', methods: ['GET'])]
    public function index(TeamsHaveMatchesRepository $teamsHaveMatchesRepository): Response
    {
        return $this->render('teams_have_matches/index.html.twig', [
            'teams_have_matches' => $teamsHaveMatchesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_teams_have_matches_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TeamsHaveMatchesRepository $teamsHaveMatchesRepository): Response
    {
        $teamsHaveMatch = new TeamsHaveMatches();
        $form = $this->createForm(TeamsHaveMatchesType::class, $teamsHaveMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teamsHaveMatchesRepository->save($teamsHaveMatch, true);

            return $this->redirectToRoute('app_teams_have_matches_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('teams_have_matches/new.html.twig', [
            'teams_have_match' => $teamsHaveMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_teams_have_matches_show', methods: ['GET'])]
    public function show(TeamsHaveMatches $teamsHaveMatch): Response
    {
        return $this->render('teams_have_matches/show.html.twig', [
            'teams_have_match' => $teamsHaveMatch,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/{id}/edit', name: 'app_teams_have_matches_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TeamsHaveMatches $teamsHaveMatch, TeamsHaveMatchesRepository $teamsHaveMatchesRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TeamsHaveMatchesType::class, $teamsHaveMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teamsHaveMatchesRepository->save($teamsHaveMatch, true);

            $this->updateRankings($entityManager);
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
