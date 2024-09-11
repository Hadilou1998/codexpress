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
            ['is_public' => true], // on filtre les notes qui sont publiques
            ['created_at' => 'DESC'], // on trie les notes par date de création
            6 // on affiche 6 notes
        );
        return $this->render('home/index.html.twig', [
            'lastNote' => $lastNotes, // on passe les notes à la vue Twig
        ]);
    }
}