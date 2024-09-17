<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

##[IsGranted('IS_AUTHENTIFICATED_FULLY')]
class CreatorController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(Request $request): Response
    {
        return $this->render('creator/profile.html.twig');
    }

    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur authentifié
        return $this->render('creator/edit.html.twig', [
            'controller_name' => 'CreatorController',
        ]);
    } 
}