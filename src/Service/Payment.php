<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Repository\OfferRepository;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class Payment extends AbstractService
{
    private string $apiKey;
    private $offer;

    public function __construct(private Stripe $stripe, OfferRepository $or)
    {
        $this->offer = $or->findOneByName('Premium');
    }

    // Générer une demande de paiement
    public function askCheckout(): ?Subscription
    {
        $domain = 'http://localhost:8000';
        // Emission de la demande à Stripe
        $checkoutSession = Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => $this->offer->getPrice() * 100,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $domain . '/success.html',
            'cancel_url' => $domain . '/cancel.html',
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