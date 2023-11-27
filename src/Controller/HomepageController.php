<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage_index')]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/clear', name: 'app_clear_database')]
    public function clearDatabase(EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeUpdate($platform->getTruncateTableSQL('your_table_name', true));

        return $this->redirectToRoute('');
    }
}
