<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/notes')] // Suffixe pour les routes du controller
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all')]
    public function all(NoteRepository $nr): Response
    {
        return $this->render('home/all.html.twig', [
            'allNotes' => $nr->findBy(['is_public' => true], ['created_at' => 'DESC']),
        ]);
    }

    #[Route('/{slug}', name: 'app_note_show')]
    public function show(string $slug, NoteRepository $nr): Response
    {
        return $this->render('home/show.html.twig', [
            'note' => $nr->findOneBySlug($slug),
        ]);
    }

    #[Route('/{username}', name: 'app_note_user')]
    public function userNotes(
        string $username,
        UserRepository $user, // On récupère le repository de l'entité User
    ): Response {
        $creator = $user->findOneByUsername($username); // On recherche l'utilisateur
        return $this->render('home/show.html.twig', [
            'note' => $creator->getNotes(), // On récupère les notes de l'utilisateur
        ]);
    }   
}