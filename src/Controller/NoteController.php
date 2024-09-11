<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/notes')]
class NoteController extends AbstractController
{
    /**
     * /notes
     * Route dédiée à l'affichage de toutes les notes
     */
    #[Route('/notes', name: 'app_notes_show')]
    public function all(NoteRepository $notes): Response
    {
        return $this->render('note/all.html.twig', [
            'notes' => $notes->findAll(),
        ]);
    }
    
    /**
     * /note
     * Route dédiée à l'affichage d'une seule note
     */

    ##[IsGranted('ROLE_USER')]
    #[Route('note/{slug}', name: 'app_note', methods: ['GET'])]
    public function show(NoteRepository $notes, string $slug): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $notes->findOneBySlug($slug),
        ]);
    }
    
    #[Route('/my-notes', name: 'app_my-note')]
    public function myNote(): Response
    {
        return $this->render('note/note.html.twig', []);
    }
}