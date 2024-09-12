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
            'JavaScript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
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
        $usersArray = []; // Initialisation du tableau pour stocker les utilisateurs

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
                    ->setTitle($faker->sentence())
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

        $networksArray = []; // Ce tableau nous servira pour conserver les objets Networks table

        foreach ($networks as $name => $url) {
            $network = new Network(); // Nouvel objet Network
            $network
                ->setName($name) // Ajoute le titre
                ->setUrl($url) // Ajoute l'url
                ->setCreator($faker->randomElement($usersArray)) // Ajoute l'utilisateur
            ;

            array_push($networksArray, $network); // Ajout de l'objet
            $manager->persist($network);
        }

        // Création des enregistrements de la table notifications
        $notifications = [
            'info'      => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/info/info-circle-solid.svg',
            'success'   => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/check/check-circle-solid.svg',
            'warning'   => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/exclamation/exclamation-circle-solid.svg',
            'danger'    => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/exclamation/exclamation-circle-solid.svg',
            'primary'   => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/info/info-circle-solid.svg',
            'secondary' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/info/info-circle-solid.svg',
            'light'     => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/info/info-circle-solid.svg',
            'dark'      => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/info/info-circle-solid.svg',
        ];

        $notificationsArray = []; // Ce tableau nous servira pour conserver les objets Notifications table

        foreach ($notifications as $type => $icon) {
            $notification = new Notification(); // Nouvel objet Notification
            $notification
                ->setTitle($faker->sentence()) // Ajoute le titre
                ->setContent($faker->paragraphs(4, true)) // Ajoute le contenu
                ->setType($type) // Ajoute le type
                ->setArchive(false) // Ajoute l'archive
                ->setNote($faker->randomElement($notesArray)) // Ajoute la note
            ;

            array_push($notificationsArray, $notification); // Ajout de l'objet
            $manager->persist($notification);
        }

        // Création des enregistrements de la table `like`
        $likes = [
            'like' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/thumbs-up/thumbs-up-solid.svg',
            'dislike' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/thumbs-down/thumbs-down-solid.svg',
            'love' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/heart/heart-solid.svg',
            'haha' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laugh/laugh-solid.svg',
            'wow' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/surprise/surprise-solid.svg',
            'sad' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/sad-tear/sad-tear-solid.svg',
            'angry' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/angry/angry-solid.svg',
        ];

        $likesArray = []; // Ce tableau nous servira pour conserver les objets Likes table

        foreach ($likes as $type => $icon) {
            $like = new Like(); // Nouvel objet Like
            $like
                ->setNote($faker->randomElement($notesArray)) // Ajoute la note
                ->setCreator($faker->randomElement($usersArray)) // Ajoute l'utilisateur
            ;

            array_push($likesArray, $like); // Ajout de l'objet
            $manager->persist($like);
        }
    }
}