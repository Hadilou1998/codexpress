<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')] // Accès permis uniquement aux utilisateurs authentifiés
class CreatorController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(): Response
    {
        return $this->render('creator/profile.html.twig');
    }

    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur authentifié
        // TODO: Formulaire de modification et traitement des données
        return $this->render('creator/edit.html.twig', [
            // TODO: Formulaire à envoyer à la vue Twig
        ]);
    }
}