<?php

namespace App\Controller;

use App\Entity\SummerMatch;
use App\Entity\TeamsHaveMatches;
use App\Entity\User;
use App\Form\SummerMatchType;
use App\Repository\SummerMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/match/date')]
class SummerMatchController extends AbstractController
{
    #[Route('/', name: 'app_summer_match_index', methods: ['GET'])]
    public function index(SummerMatchRepository $summerMatchRepository, PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted ( attribute: "IS_AUTHENTICATED_FULLY");
        $user = $this->getUser();
        $userEntity = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        $query = $summerMatchRepository->findBy(['user' => $userEntity]);
        $matches = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return match ($user->isVerified()) {
            true => $this->render('summer_match/index.html.twig', ['summer_matches' => $matches]),
            false => $this->render("security/verify_email.html.twig"),
        };
    }

    #[Route('/new', name: 'app_summer_match_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SummerMatchRepository $summerMatchRepository, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $summerMatch = new SummerMatch($user);
        $form = $this->createForm(SummerMatchType::class, $summerMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $summerMatchRepository->save($summerMatch, true);

            $teamsHaveMatches1 = new TeamsHaveMatches();
            $teamsHaveMatches1->setMatchesHaveTeams($summerMatch);
            $teamsHaveMatches1->setNrPoints(1);
            $teamsHaveMatches1->setGoals(0);
            $teamsHaveMatches1->setTeamsHaveMatches(null);
            $entityManager->persist($teamsHaveMatches1);
            $entityManager->flush();

            $teamsHaveMatches2 = new TeamsHaveMatches();
            $teamsHaveMatches2->setMatchesHaveTeams($summerMatch);
            $teamsHaveMatches2->setNrPoints(1);
            $teamsHaveMatches2->setGoals(0);
            $teamsHaveMatches2->setTeamsHaveMatches(null);
            $entityManager->persist($teamsHaveMatches2);
            $entityManager->flush();

            return $this->redirectToRoute('app_summer_match_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('summer_match/new.html.twig', [
            'summer_match' => $summerMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_summer_match_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SummerMatch $summerMatch, SummerMatchRepository $summerMatchRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SummerMatchType::class, $summerMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $summerMatchRepository->save($summerMatch, true);

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
}
