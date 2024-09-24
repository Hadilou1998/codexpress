<?php

namespace App\Service;

use Stripe\Stripe;
use App\Entity\Subscription;
use App\Repository\OfferRepository;
use App\Service\AbstractService;
use Stripe\Checkout\Session;

class Payment extends AbstractService
{
    private $offer; // Offre Premium
    private $domain = 'http://localhost:8000'; // Adresse du domaine

    public function __construct(private Stripe $stripe, OfferRepository $or)
    {
        $this->offer = $or->findOneByName('Premium'); // Récupération de l'offre Premium
    }

    // Générer une demande de paiement
    public function askCheckout(): ?Subscription
    {
        Stripe::setApiKey($this->parameter->get('STRIPE_API_KEY')); // Etablissement de la connexion (API)

        header('Content-Type: application/json'); // Définition du type de contenu de la requête
        
        $checkoutSession = Session::create([
            'line_items' => [[
                'price' => $this->offer->getPrice() * 100,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->domain . '/success.html',
            'cancel_url' => $this->domain . '/cancel.html',
            'automatic_tax' => [
                'enabled' => true
            ],
        ]);
        return 'OK'; // templates/subscription/index.html.twig
    }

    // Traitement du role des utilisateurs

    // Génération de la facture

    // Notifications des emails
}
?>