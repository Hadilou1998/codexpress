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

        $users = []; // Initialize users array
        $notes = []; // Initialize notes array

        // 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName; // Génére un username aléatoire
            $usernameFinal = $this->slug->slug($username); // Username en slug
            $user =  new User();
            $user
                ->setEmail($usernameFinal . '@' . $faker->freeEmailDomain)
                ->setUsername($username)
                ->setPassword($this->hash->hashPassword($user, 'saiyan'))
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
            $users[] = $user; // Add user to users array

            for ($j=0; $j < 10; $j++) { 
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
                $notes[] = $note; // Add note to notes array
            }
        }

        // Création des likes
        $likes = [];

        for ($i = 0; $i < 10; $i++) {
            $user = $users[array_rand($users)];
            $note = $notes[array_rand($notes)];
            $like = new Like();
            $like
                ->setCreator($user)
                ->setNote($note)
                ;
            $manager->persist($like);
            $likes[] = $like;
        }

        // Création des notifications
        $notifications = [];

        for ($i = 0; $i < 10; $i++) {
            $user = $users[array_rand($users)];
            $note = $notes[array_rand($notes)];
            $notification = new Notification();
            $notification
                ->setTitle($faker->sentence())
                ->setContent($faker->randomHtml())
                ->setType('like')
                ->setArchived(false)
                ->setNote($note)
                ;
            $manager->persist($notification);
            $notifications[] = $notification;
        }

        // Création des offres
        $offers = [];

        for ($i = 0; $i < 10; $i++) {
            $user = $users[array_rand($users)];
            $offer = new Offer();
            $offer
                ->setName($faker->sentence())
                ->setPrice($faker->randomFloat(2, 10, 1000))
                ->setFeatures($faker->randomHtml())
                ;
            $manager->persist($offer);
            $offers[] = $offer;
        }

        // Création des subscriptions
        $subscriptions = [];

        for ($i = 0; $i < 10; $i++) {
            $offer = $offers[array_rand($offers)];
            $user = $users[array_rand($users)];
            $subscription = new Subscription();
            $subscription
                ->setOffer($offer)
                ->setCreator($user)
                ->setStartDate((new \DateTimeImmutable())->modify('+1 month'))
                ->setEndDate((new \DateTimeImmutable())->modify('+1 year'))
                ;
            $manager->persist($subscription);
            $subscriptions[] = $subscription;
        }

        // Création des views
        $views = [];
        $notes = $manager->getRepository(Note::class)->findAll();
        $ips = ['192.168.1.1', '192.168.1.2', '192.168.1.3', '192.168.1.4', '192.168.1.5'];
        
        for ($i = 0; $i < 10; $i++) {
            $note = $notes[array_rand($notes)];
            $ip = $ips[array_rand($ips)];
            $view = new View();
            $view
                ->setNoteId($note->getId())
                ->setIpAddress($ip)
                ;
            $manager->persist($view);
            $views[] = $view;
        }

        $manager->flush();
    }
}