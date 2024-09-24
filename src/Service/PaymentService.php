<?php

    namespace App\Service;

    use Stripe\Stripe;
    use App\Entity\Subscription;
    use Stripe\Checkout\Session;
    use App\Repository\OfferRepository;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Doctrine\ORM\EntityManagerInterface;

    class PaymentService
    {
        private $offer, $domain, $apiKey, $user;

        public function __construct(
            private ParameterBagInterface $parameter,
            private OfferRepository $or,
            private readonly Security $security,
            private EntityManagerInterface $em
        ) {
            $this->offer = $or->findOneByName('Premium'); // Récupération de l'offre Premium
            $this->apiKey = $this->parameter->get('STRIPE_API_SK'); // Récupération de la clé API Stripe
            $this->domain = 'https://127.0.0.1:8000/en'; // Adresse du domaine
            $this->user = $security->getUser(); // Récupération de l'utilisateur connecté
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
                'success_url' => $this->domain . '/payment-success',
                'cancel_url' => $this->domain . '/payment-cancel',
                'automatic_tax' => [
                    'enabled' => false,
                ],
            ]);
            
            return $checkoutSession;
        }

        // Traitement du role des utilisateurs
        public function addSubscription(): ?Subscription
        {
            $subscription = new Subscription();
            $subscription
                ->setCreator($this->user)
                ->setOffer($this->offer)
                ->setStartDate(new \DateTimeImmutable())
                ->setEndDate(new \DateTimeImmutable('+30 days'))
                ;
                $this->em->persist($subscription);
                $this->em->flush();
            
            $userRoles = $this->user->getRoles();
            $userRoles[] = 'ROLE_PREMIUM';
            $this->user->setRoles($userRoles);
            $this->em->persist($this->user);
            $this->em->flush();

            return $subscription;
        }

        // Génération de la facture

        // Notifications des emails
    }
?>