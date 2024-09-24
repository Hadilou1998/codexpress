<?php

    namespace App\Service;

    use Stripe\Stripe;
    use App\Repository\OfferRepository;
    use App\Service\AbstractService;
    use Stripe\Checkout\Session;
    use Symfony\Component\HttpFoundation\RedirectResponse;

    class PaymentService extends AbstractService
    {
        private $offer; // Offre Premium
        private $domain = 'http://localhost:8000'; // Adresse du domaine
        private $apiKey; // Clé API Stripe

        public function __construct(private Stripe $stripe, private RedirectResponse $redirectResponse, OfferRepository $or)
        {
            $this->offer = $or->findOneByName('Premium'); // Récupération de l'offre Premium
        }

        /**
         * askCheckout()
         * Méthode permettant de créer une session de paiement Stripe
         * @return Stripe\Checkout\Session
         */
        // Générer une demande de paiement
        public function askCheckout(): ?Session
        {
            Stripe::setApiKey($this->apiKey); // Définition de la clé API Stripe
            $checkoutSession = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $this->offer->getPrice() * 100, // Stripe utilise des centimes
                        'product_data' => [ // Les informations du produit sont personnalisables
                            'name' => $this->offer->getName(),
                        ],  
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->domain . '/success.html',
                'cancel_url' => $this->domain . '/cancel.html',
                'automatic_tax' => [
                    'enabled' => true
                ],
            ]);
            
            return $checkoutSession;
        }

        // Traitement du role des utilisateurs

        // Génération de la facture

        // Notifications des emails
    }
    
?>