<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Notification;
use App\Entity\Network;
use App\Entity\Like;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

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
            'JS' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
            'PHP' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-plain.svg',
            'SQL' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-plain.svg',
            'JSON' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/json/json-plain.svg',
            'Python' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-plain.svg',
            'Ruby' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ruby/ruby-plain.svg',
            'C++' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-plain.svg',
            'Go' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/go/go-wordmark.svg',
            'bash' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bash/bash-plain.svg',
            'Markdown' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/markdown/markdown-original.svg',
            'Java' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/java/java-original-wordmark.svg',
        ];

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

        $notesArray = []; // Initialisation du tableau pour stocker les notes

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
                ->setImage($faker->imageUrl(640, 480, 'people', true));
            $manager->persist($user);
            $usersArray[] = $user; // Ajout de l'utilisateur au tableau

            for ($j=0; $j < 10; $j++) { 
                $note = new Note();
                $note
                    ->setTitle($faker->sentence(3))
                    ->setSlug($this->slug->slug($note->getTitle()))
                    ->setContent($faker->paragraphs(4, true))
                    ->setPublic($faker->boolean(50))
                    ->setViews($faker->numberBetween(100, 10000))
                    ->setCreator($user)
                    ->setCategory($faker->randomElement($categoryArray));
                $manager->persist($note);
                $notesArray[] = $note; // Ajout de la note au tableau
            }
        }

        // Création des enregistrements `network`
        $networks = [
            'Twitter' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/twitter/twitter-original.svg',
            'Facebook' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/facebook/facebook-original.svg',
            'Instagram' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/instagram/instagram-original.svg',
            'LinkedIn' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/linkedin/linkedin-plain.svg',
        ];

        // $networksArray = []; // Ce tableau nous servira pour conserver les objets Networks table

        foreach ($networks as $name => $url) {
            $network = new Network(); // Nouvel objet Network
            $network
                ->setName($name) // Ajoute le titre
                ->setUrl($url) // Ajoute l'url
                ->setCreator($faker->randomElement($usersArray)); // Ajoute l'utilisateur

            // array_push($networksArray, $network); // Ajout de l'objet
            $manager->persist($network);
        }

        // Création des enregistrements `notification`
        $notifications = [
            'New note' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
            'New comment' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
            'New follower' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
            'New message' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
            'New notification' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',          
        ];

        // $notificationsArray = []; // Ce tableau nous servira pour conserver les objets Networks table
        foreach ($notifications as $title => $url) {
            $notification = new Notification(); // Nouvel objet Notification
            $notification
                ->setTitle($title) // Ajouter le titre
                ->setContent($faker->paragraphs(4, true)) // Ajouter le contenu
                ->setType($faker->randomElement(['info', 'success', 'warning', 'danger'])) // Ajouter le type
                ->setArchive($faker->boolean(50)) // Ajouter le booléen
                ->setNote($faker->randomElement($notesArray)); // Ajoute la note
            $manager->persist($notification);
        }

        // Création des enregistrements `like`
        $likes = [
            'New like' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
            'New comment' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
            'New follower' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
            'New message' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
            'New notification' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg',
        ];

        // $likesArray = []; // Ce tableau nous servira pour conserver les objets Networks table
        foreach ($likes as $title => $url) {
            $like = new Like(); // Nouvel objet Like
            $like
                ->setNote($faker->randomElement($notesArray)) // Ajouter la note
                ->setCreator($faker->randomElement($usersArray)); // Ajouter l'utilisateur
            $manager->persist($like);
        }


        $manager->flush();
    } 
}