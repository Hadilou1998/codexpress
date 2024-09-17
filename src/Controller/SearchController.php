<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, PaginatorInterface $paginator, NoteRepository $nr): Response
    {
        $searchQuery = $request->query->get('q');
        
        if ($searchQuery === null) {
            return $this->render('search/results.html.twig');
        }

        $query = $paginator->paginate(
            $nr->findBySearch($searchQuery),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('search/results.html.twig', [
            'searchQuery' => $searchQuery,
            'notes' => $query,
        ]);
    }
}
