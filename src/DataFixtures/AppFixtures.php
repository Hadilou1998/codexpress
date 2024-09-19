<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Network;
use App\Entity\Category;
use App\Entity\Like;
use App\Entity\Notification;
use App\Entity\Offer;
use App\Entity\Subscription;
use App\Entity\View;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $slug = null;
    private $hash = null;

    public function __construct(
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $hasher
    ) {
        $this->slug = $slugger;
        $this->hash = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de catégories
        $categories = [
            'HTML' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-plain.svg',
            'CSS' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-plain.svg',
            'JavaScript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
            'PHP' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-plain.svg',
            'SQL' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-plain.svg',
            'JSON' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/json/json-plain.svg',
            'Python' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-plain.svg',
            'Ruby' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ruby/ruby-plain.svg',
            'C++' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-plain.svg',
            'Go' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/go/go-original-wordmark.svg',
            'bash' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bash/bash-plain.svg',
            'Markdown' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/markdown/markdown-original.svg',
            'Java' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/java/java-original-wordmark.svg',
        ];

        $networks = ['github', 'twitter', 'linkedin', 'facebook', 'reddit', 'instagram', 'youtube'];

        $categoryArray = []; // Ce tableau nous servira pour conserver les objets Category

        foreach ($categories as $title => $icon) {
            $category = new Category(); // Nouvel objet Category
            $category
                ->setTitle($title) // Ajoute le titre
                ->setIcon($icon) // Ajoute l'icone
            ;

            array_push($categoryArray, $category); // Ajout de l'objet
            $manager->persist($category);
        }
        // Admin
        $user =  new User();
        $user
            ->setEmail('kakarot@codexpress.fr')
            ->setUsername('Kakarot93')
            ->setPassword($this->hash->hashPassword($user, 'saiyan'))
            ->setRoles(['ROLE_ADMIN'])
            ;
        $manager->persist($user);

        for ($d=0; $d < 3; $d++) {
            $network = new Network();
            $network
                ->setName($faker->randomElement($networks))
                ->setUrl('https://' . $network->getName() . '.com/')
                ->setCreator($user)
                ;
            $manager->persist($network);
        }

        for ($y=0; $y < 10; $y++) { 
            $note = new Note();
            $note
                ->setTitle($faker->sentence())
                ->setSlug($this->slug->slug($note->getTitle()))
                ->setContent($faker->randomHtml())
                ->setPublic($faker->boolean(50))
                ->setViews($faker->numberBetween(100, 10000))
                ->setCreator($user)
                ->setCategory($faker->randomElement($categoryArray))
                ;
            $manager->persist($note);
        }

        // 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName; // Génére un username aléatoire
            $usernameFinal = $this->slug->slug($username); // Username en slug
            $user =  new User();
            $user
                ->setEmail($usernameFinal . '@' . $faker->freeEmailDomain)
                ->setUsername($username)
                ->setPassword($this->hash->hashPassword($user, 'admin'))
                ->setRoles(['ROLE_USER'])
                ;
            for ($z=0; $z < 3; $z++) {
                $network = new Network();
                $network
                    ->setName($faker->randomElement($networks))
                    ->setUrl('https://' . $network->getName() . '.com/' . $usernameFinal)
                    ->setCreator($user)
                    ;
                $manager->persist($network);
            }
            $manager->persist($user);

            for ($j=0; $j < 10; $j++) { 
                $note = new Note();
                $note
                    ->setTitle($faker->sentence())
                    ->setSlug($this->slug->slug($note->getTitle()))
                    ->setContent($faker->randomHtml())
                    ->setPublic($faker->boolean(50))
                    ->setPremium($faker->boolean(50))
                    ->setViews($faker->numberBetween(100, 10000))
                    ->setCreator($user)
                    ->setCategory($faker->randomElement($categoryArray))
                    ;
                $manager->persist($note);
            }
        }

        // Création de 10 likes
        $likes = [
            'like' => 'like',
            'dislike' => 'dislike',
            'love' => 'love',
            'haha' => 'haha',
            'wow' => 'wow',
            'sad' => 'sad',
            'angry' => 'angry',
            'care' => 'care',
            'celebrate' => 'celebrate',
        ];

        for ($k=0; $k < 10; $k++) {
            $user = $faker->randomElement($manager->getRepository(User::class)->findAll());
            $note = $faker->randomElement($manager->getRepository(Note::class)->findAll());

            $likes = new Like();
            $likes
                ->setCreator($user)
                ->setNote($note)
                ;
            $manager->persist($likes);
        }

        // Creation de 10 notifications
        $notifications = [
            'like' => 'I like it',
            'dislike' => 'I dislike it',
            'love' => 'I love it',
            'haha' => 'I\'m laughing',
            'wow' => 'I\'m amazed',
            'sad' => 'I\'m sad',
            'angry' => 'I\'m angry',
            'care' => 'I care',
            'celebrate' => 'I will celebrate',
        ];

        foreach ($notifications as $key => $value) {
            $notification = new Notification();
            $notification
                ->setTitle($faker->sentence()) // Titre de la notification
                ->setContent($faker->sentence()) // Contenu de la notification
                ->setType($key) // Type de la notification
                ->setArchived(false) // La notification n'est pas archivée
                ->setNote($note) // La notification est liée à une note
                ;
            $manager->persist($notification);
        }

        // Création de 10 offres
        $offers = [
            'premium' => 'Premium',
            'pro' => 'Pro',
            'free' => 'Free',
            'basic' => 'Basic',
            'standard' => 'Standard',
        ];

        for ($l=0; $l < 10; $l++) {
            $user = $faker->randomElement($manager->getRepository(User::class)->findAll());
            $note = $faker->randomElement($manager->getRepository(Note::class)->findAll());
            $category = $faker->randomElement($manager->getRepository(Category::class)->findAll());

            $offers = new Offer();
            $offers
                ->setName($faker->sentence()) // Nom de l'offre
                ->setPrice($faker->randomFloat(2, 10, 100)) // Prix de l'offre
                ->setFeatures($faker->sentence()) // Caractéristiques de l'offre
                ;
            $manager->persist($offers);
        }
        
        // Création des subscriptions
        $subscriptions = [
            'premium' => 'premium',
            'pro' => 'pro',
            'free' => 'free',
            'basic' => 'basic',
           'standard' =>'standard',
        ];

        for ($m=0; $m < 10; $m++) {
            $user = $faker->randomElement($manager->getRepository(User::class)->findAll());
            $note = $faker->randomElement($manager->getRepository(Note::class)->findAll());
            $category = $faker->randomElement($manager->getRepository(Category::class)->findAll());

            $subscriptions = new Subscription();
            $subscriptions
                ->setOffer($faker->randomElement($manager->getRepository(Offer::class)->findAll())) // Offre de la subscription
                ->setCreator($user) // Utilisateur de la subscription
                ->setStartDate(new \DateTimeImmutable()) // Date de début de la subscription
                ->setEndDate(new \DateTimeImmutable()) // Date de fin de la subscription
                ;
            $manager->persist($subscriptions);
        }
        
        // Création des views
        $views = [
            'view' => 'view',
            'edit' => 'edit',
            'delete' => 'delete',
            'comment' => 'comment',
           'share' =>'share',
        ];



        for ($n=0; $n < 10; $n++) {
            $note = $faker->randomElement($manager->getRepository(Note::class)->findAll());

            $views = new View();
            $views
                ->setNote($note) // Note regardée
                ->setIpAddress($faker->ipv4()) // Adresse IP de l'utilisateur
                ;
            $manager->persist($views);
        }

        $manager->flush();
    }
}