<?php

    namespace App\Service;

    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

        public function upload(UploadedFile $file): string
        {
            try {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = uniqid('image') . '.' . $file->guessExtension();
                $file->move($this->param->get('uploads_images_directory'), $filename);

                return $this->param->get('uploads_images_directory') . '/' . $filename;              
            } catch (\Exception $e) {
                throw new \Exception('Erreur lors du téléversement du fichier: ' . $e->getMessage());
            }
        }
    }
?>