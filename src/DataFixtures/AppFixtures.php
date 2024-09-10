<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use Faker\Factory;
use App\Entity\User;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

        foreach ($categories as $title => $icon) {
            $category = new Category(); // Nouvel objet Category
            $category->setTitle($title); // Ajout du titre
            $category->setIcon($icon); // Ajout de l'icône
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
            $manager->persist($user); // Persiste l'objet
        }

        $manager->flush();
    }
}