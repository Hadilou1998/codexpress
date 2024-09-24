<?php

    namespace App\Service;

    use App\Entity\Subscription;
    use Stripe\Stripe;
    use Stripe\Checkout\Session;

    class Payment extends AbstractService
    {
        // private string $apiKey = $this->parameter->get('STRIPE_API_SK');

        public function __construct(private Stripe $stripe)
        {
            $this->stripe = new Stripe(); // Instanciation de Stripe
        }

        // Générer une demande de paiement
        public function askCheckout(): ?Subscription
        {
        }

        // Traitement du role des utilisateurs

        // Génération de la facture

        // Notifications des emails
    }
    
?>