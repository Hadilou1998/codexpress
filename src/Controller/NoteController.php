<?php

namespace App\Controller;

use App\Form\NoteType;
use App\Entity\Note;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/notes')] // Suffixe pour les routes du controller
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all', methods: ['GET'])]
    public function all(NoteRepository $nr): Response
    {
        return $this->render('note/all.html.twig', [
            'allNotes' => $nr->findBy(['is_public' => true], ['created_at' => 'DESC']),
        ]);
    }

    #[Route('/n/{slug}', name: 'app_note_show', methods: ['GET'])]
    public function show(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug);
        if ($note === null) {
            throw $this->createNotFoundException('Note not found');
        } else {
            if ($note->isPublic()) {
                $creatorNotes = $nr->findByCreator($note->getCreator());
                return $this->render('note/show.html.twig', [
                    'note' => $note,
                    'creatorNotes' => $creatorNotes,
                ]);
            } else {
                throw $this->createAccessDeniedException('This note is private');
            }
        }
    }

    #[Route('/u/{username}', name: 'app_note_user', methods: ['GET'])]
    public function userNotes(
        string $username,
        UserRepository $user, // On récupère le repository de l'entité User
    ): Response {
        $creator = $user->findOneByUsername($username); // On recherche l'utilisateur
        return $this->render('note/user.html.twig', [
            'creator' => $creator, // On envoie les données de l'utilisateur à la vue Twig
            'userNotes' => $creator->getNotes(), // On récupère les notes de l'utilisateur
        ]);
    }
    
    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if (!$this->getUser()) { // Si l'utilisateur n'est pas connecté
            $this->addFlash('error', 'Vous devez être connecté pour ajouter une note'); // On affiche un message d'erreur
            return $this->redirectToRoute('app_login'); // On redirige vers la page de connexion
        }

        $form = $this->createForm(NoteType::class); // Chargement du formulaire
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        // Traitement des données
        if ($form->isSubmitted() && $form->isValid()) {
            $note = new Note();
            $note
                ->setTitle($form->get('title')->getData())
                ->setSlug($slugger->slug($note->getTitle()))
                ->setContent($form->get('content')->getData())
                ->setPublic($form->get('is_public')->getData())
                ->setCategory($form->get('category')->getData())
                ->setCreator($form->get('creator')->getData())
            ;
            $em->persist($note);
            $em->flush();

            $this->addFlash('success', 'Note créée avec succès');
            return $this->redirectToRoute('app_note_show', ['slug' => $note->getSlug()]); // On redirige vers la page de la note créée
        }
        return $this->render('note/new.html.twig', [
            'noteForm' => $form
        ]);
    }

    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, NoteRepository $nr, Request $request, EntityManagerInterface $em): Response
    {
        $note = $nr->findOneBySlug($slug); // On recherche la note à modifier

        if ($note->getCreator() !== $this->getUser()) { // Si la note n'existe pas ou si elle ne correspond pas à l'utilisateur connecté
            $this->addFlash('error', 'Vous etes autorisez à modifier la note'); // On ajoute un message de l'utilisateur
            return $this->redirectToRoute('app_note_show', ['slug' => $slug]); // On redirige vers la page de la note
        }

        $form = $this->createForm(NoteType::class, $note); // On charge le formulaire avec les données de la note
        $form = $form->handleRequest($request); // On récupère les données du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($note);
            $em->flush();

            $this->addFlash('success', 'Note modifiée avec succès');
            return $this->redirectToRoute('app_note_show', ['slug' => $note->getSlug()]); // On redirige vers la page de la note modifiée
        }

        return $this->render('note/edit.html.twig', ['noteForm' => $form]); // On affiche la page de modification
    }

    #[Route('/delete/{slug}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug); // On recherche la note à supprimer
        $this->addFlash('success', 'La note a bien été supprimée.'); // On ajoute un message de succès
        return $this->redirectToRoute('app_note_user'); // On redirige vers la page de l'utilisateur
    }
}