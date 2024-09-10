<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use Faker\Factory;
use App\Entity\User;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Note;

class AppFixtures extends Fixture
{
    private $slug = null;
    private $hash = null;

    public function __construct(
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $hasher
        )
    {
        $this->slug = $slugger;
        $this->hash = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Création des catégories
        # Tableau contenant les catégories
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

        $categoryArray = []; // Tableau nous servant à conserver les objets Category

        foreach ($categories as $title => $icon) {
            $category = new Category(); // Nouvel objet Category
            $category->setTitle($title); // Ajout du titre
            $category->setIcon($icon); // Ajout de l'icône
            array_push($categoryArray, $category); // Ajout de l'objet à notre tableau
            $manager->persist($category); // Persiste l'objet
        }

        // 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName(); // Génère un username aléatoire
            $usernameFinal = $this->slug->slug($username); // Username en slug
            $user = new User(); // Nouvel objet User
            $user->setEmail($usernameFinal . $faker->freeEmailDomain); // Ajout de l'email
            $user->setUsername($faker->userName()); // Ajout du nom d'utilisateur
            $user->setPassword($this->hash->hashPassword($user, 'admin')); // Ajout du mot de passe
            $user->setRoles(['ROLE_USER']); // Ajout du rôle
        }

        for ($j=0; $j < 10; $j++) {
            $note = new Note(); // Nouvel objet Note
            $note->setTitle($faker->sentence()); // Ajout du titre
            $note->setSlug($this->slug->slug($note->getTitle())); // Ajout du slug
            $note->setContent($faker->paragraph(4, true)); // Ajout du contenu
            $note->setPublic($faker->boolean(50)); // Ajout du statut public
            $note->setViews($faker->numberBetween(100, 1000)); // Ajout du nombre de vues
            $note->setCreator($user); // Ajout de l'auteur
            $note->setCategory($faker->randomElement($categoryArray)); // Ajout de la catégorie
            $manager->persist($note); // Persiste l'objet
        }

        $manager->flush();
    }
}