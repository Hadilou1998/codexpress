<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    //  Recherche par mot-clé dans le tite et le contenu de note publiques
    //  public function findBySearch(string $query): array
    //  {
    //      $qb = $this->createQueryBuilder('n');
    //      $qb
    //         ->where('n.is_public = true') // Filtre les notes publiques
    //         ->andWhere('n.title LIKE :query OR n.content LIKE :query') // Recherche par mot-clé
    //        ->setParameter('query', '%' . $query . '%') // Ajoute le mot-clé à la requête
    //        ->orderBy('n.created_at', 'DESC') // Trie par date de création
    //        ->setMaxResults(100) // Limite à 100 résultats
    //             ;
    //         return $qb->getQuery()->getResult();
    //      }

    /**
    * FindByQuery
    *Méthode pour la recherche de note dans l'application CodeXpress
    *@param string $query
    *@return array
    */
    public function findByQuery($query): array
    {
        return $this->createQueryBuilder('n')
        ->where('n.is_public = true')
            ->andWhere('n.title LIKE :q OR n.content LIKE :q')
            ->setParameter('q', '%'. $query .'%')
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    // 3 dernières publiques créées par l'id de l'utilisateur
    public function findByCreator($id): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.is_public = true')
            ->andWhere('n.creator = :id')
            ->setParameter('id', $id)
            ->orderBy('n.created_at', 'DESC') // Trie par date de création
            ->setMaxResults(3) // Limite à 3 résultats
            ->getQuery()
            ->getResult()
            ;
    }

    //    public function findOneBySomeField($value): ?Note
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}