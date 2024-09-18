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
        // $searchQuery = $request->query->get('q');
        
        if ($request->get('q') === null) {
           return $this->render('search/results.html.twig');
        } // Si l'accès est refusé, on renvoie vers la page d'accueil
        
        $pagination = $paginator->paginate(
            $nr->findByQuery($request->get('q')),
            $request->query->getInt('page', 1),
            24
        );

        return $this->render('search/results.html.twig', [
            'allNotes' => $pagination,
            'searchQuery' => $request->get('q')
        ]);
    }
}