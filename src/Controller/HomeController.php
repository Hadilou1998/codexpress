<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(NoteRepository $nr): Response
    {
        $lastNotes = $nr->findBy(
            ['is_public' => true], // On filtre les notes publiques
            ['created_at' => 'DESC'], // On trie les notes par date de création
            6 // On limite les résultats à 6
        );
        return $this->render('home/index.html.twig', [
            'lastNotes' => $lastNotes, // On envoie les notes à la vue Twig
            'totalNotes' => count($nr->findAll()), // On envoie le nombre total de notes à la vue Twig
        ]);
    }
}