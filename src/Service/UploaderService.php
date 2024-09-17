<?php

    namespace App\Service;

    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

    /**
     * Service de téléversement de fichiers dans l'application CodeXpress
     * - Images (.jpg, .jpeg, .png, .gif)
     * - Documents (Plus tard)
     * 
     * Méthodes : Téléverser, Supprimer
     */
    class UploaderService
    {
        private $param;

        public function __construct(ParameterBagInterface $parameterBag)
        {
            $this->param = $parameterBag;
        }

        public function upload($file): void
        {
            // TODO: Implémenter le téléversement de fichiers
        }
    }
?>