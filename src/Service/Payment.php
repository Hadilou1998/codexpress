<?php

namespace App\Service;

use Stripe\Stripe;

class Payment extends AbstractService
{
    // private string $apiKey = $this->parameter->get('STRIPE_API_SK');

    public function __construct(private Stripe $stripe)
    {
        $this->stripe = new Stripe(); // Instanciation de Stripe
    }

    // Générer une demande de paiement
    

    // Traitement du role des utilisateurs
}