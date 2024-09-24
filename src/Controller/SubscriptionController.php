<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PaymentService;

class SubscriptionController extends AbstractController
{
    #[Route('/payment-success', name: 'app_payment_success')]
    public function paymentSuccess(): Response
    {
        return $this->render('subscription/payment-success.html.twig');
    }

    #[Route('/payment-cancel', name: 'app_payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('subscription/payment-cancel.html.twig');
    }

    #[Route('/subscription', name: 'app_subscription')]
    public function index(PaymentService $ps): Response
    {
        header('Content-Type: application/json'); // Définition du type de contenu de la requête
        return $this->json($ps->askCheckout()); // templates/subscription/index.html.twig
        header('HTTP/1.1 200 OK'); // Définition du code de réponse
        header('location: ' . $ps->askCheckout()->url); // Redirection vers la page de paiement
        return $this->render('subscription/index.html.twig');
    }
}
