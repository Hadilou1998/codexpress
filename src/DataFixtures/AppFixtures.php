<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Note;
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
                ->setCreator($user) // Ajoute l'utilisateur
            ;

            array_push($networksArray, $network); // Ajout de l'objet
            $manager->persist($network);
        }

        // Création des enregistrements de la table `like`
        $notes = $manager->getRepository(Note::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($notes as $note) {
            $note->setLikes($faker->numberBetween(0, 100));
            $manager->persist($note);
            $user = $faker->randomElement($users);
            $like = new Like();
            $like->setNote($note)
            ->setCreator($user);
            $manager->persist($like);
        }

        $manager->flush();
    }
}