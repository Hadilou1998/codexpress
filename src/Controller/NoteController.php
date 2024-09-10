<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{
    #[Route('/notes', name: 'app_notes')]
    public function notes(NoteRepository $notes): Response
    {
        return $this->render('home/notes.html.twig', [
            'notes' => $notes->findAll()
        ]);
    }
    #[Route('/notes?id=', name: 'app_note')]
    public function note(): Response
    {
        return $this->render('home/note.html.twig', []);
    }
    #[Route('/my-notes', name: 'app_my_notes')]
    public function myNotes(): Response
    {
        return $this->render('home/my_notes.html.twig', []);
    }
}
