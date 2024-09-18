<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET', 'POST'])]
    public function search(Request $request, NoteRepository $nr, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->get('q');

        if (!$searchQuery) {
            return $this->render('search/results.html.twig');
        } // Si l'accès à la page se fait sans le formulaire on redirige sans paramètres
        
        $pagination = $paginator->paginate(
            $nr->findByQuery($searchQuery),
            $request->query->getInt('page', 1),
            24
        );
        return $this->render('search/results.html.twig', [
            'allNotes' => $pagination,
            'searchQuery' => $searchQuery
        ]);
    }
}
