<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function index(): Response
    {
        return $this->render('subscription/index.html.twig', [
            'controller_name' => 'SubscriptionController',
        ]);
    }
}
