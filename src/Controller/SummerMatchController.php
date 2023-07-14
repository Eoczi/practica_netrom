<?php

namespace App\Controller;

use App\Entity\Ranking;
use App\Entity\SummerMatch;
use App\Entity\Team;
use App\Entity\TeamsHaveMatches;
use App\Form\SummerMatchEditType;
use App\Form\SummerMatchType;
use App\Repository\SummerMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/summer/match')]
class SummerMatchController extends AbstractController
{
    #[Route('/', name: 'app_summer_match_index', methods: ['GET'])]
    public function index(SummerMatchRepository $summerMatchRepository): Response
    {
        return $this->render('summer_match/index.html.twig', [
            'summer_matches' => $summerMatchRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_summer_match_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SummerMatchRepository $summerMatchRepository, EntityManagerInterface $entityManager): Response
    {
        $summerMatch = new SummerMatch();
        $form = $this->createForm(SummerMatchType::class, $summerMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $summerMatchRepository->save($summerMatch, true);

            $teamsHaveMatches1 = new TeamsHaveMatches();
            $teamsHaveMatches1->setMatchesHaveTeams($summerMatch);
            $teamsHaveMatches1->setNrPoints(0);
            $entityManager->persist($teamsHaveMatches1);
            $entityManager->flush();

            $teamsHaveMatches2 = new TeamsHaveMatches();
            $teamsHaveMatches2->setMatchesHaveTeams($summerMatch);
            $teamsHaveMatches2->setNrPoints(0);
            $entityManager->persist($teamsHaveMatches2);
            $entityManager->flush();

            return $this->redirectToRoute('app_summer_match_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('summer_match/new.html.twig', [
            'summer_match' => $summerMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_summer_match_show', methods: ['GET'])]
    public function show(SummerMatch $summerMatch,EntityManagerInterface $entityManager): Response
    {
        try {
            $teamRepository = $entityManager->getRepository(Team::class);
            if ($summerMatch->getWinner() != null)
                $team = $teamRepository->find($summerMatch->getWinner());
            else
                $team = $teamRepository->findAll();
        }
        catch(\Exception $exception){
            dd($exception->getMessage());
        }

        return $this->render('summer_match/show.html.twig', [
            'summer_match' => $summerMatch,
            'team' => $team,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_summer_match_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SummerMatch $summerMatch, SummerMatchRepository $summerMatchRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SummerMatchEditType::class, $summerMatch);
        $form->handleRequest($request);
        if ($summerMatch->getWinner()?->getName())
            $oldWinner = $summerMatch->getWinner()->getName();
        else
            $oldWinner = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $summerMatchRepository->save($summerMatch, true);
            if ($summerMatch?->getWinner())
                if ($oldWinner != $summerMatch->getWinner()->getName())
                    $this->updatePoints($entityManager);

            return $this->redirectToRoute('app_summer_match_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('summer_match/edit.html.twig', [
            'summer_match' => $summerMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_summer_match_delete', methods: ['POST'])]
    public function delete(Request $request, SummerMatch $summerMatch, SummerMatchRepository $summerMatchRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$summerMatch->getId(), $request->request->get('_token'))) {
            $summerMatchRepository->remove($summerMatch, true);
        }

        return $this->redirectToRoute('app_summer_match_index', [], Response::HTTP_SEE_OTHER);
    }

    public function updatePoints(EntityManagerInterface $entityManager): void
    {
        $summerMatchRepository = $entityManager->getRepository(SummerMatch::class);
        $teamsHaveMatchesRepository = $entityManager->getRepository(TeamsHaveMatches::class);
        $teams= $teamsHaveMatchesRepository->findAll();

        foreach($teams as $team){

        }

    }
}
