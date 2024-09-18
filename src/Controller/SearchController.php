<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, NoteRepository $nr, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('q');
        
        if (!$searchQuery) {
           return $this->render('search/results.html.twig');
        } // Si l'accès est refusé, on renvoie vers la page d'accueil
        
        $query = $paginator->paginate(
            $nr->findByQuery($searchQuery),
            $request->query->getInt('page', 1),
            24
        );

        return $this->render('search/results.html.twig', [
            'searchQuery' => $searchQuery,
            'allNotes' => $query,

        ]);
    }
}