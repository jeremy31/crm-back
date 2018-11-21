<?php

namespace App\Repository;

use App\Entity\ProductDeclension;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductDeclension|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductDeclension|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductDeclension[]    findAll()
 * @method ProductDeclension[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductDeclensionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductDeclension::class);
    }

//    /**
//     * @return ProductDeclension[] Returns an array of ProductDeclension objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductDeclension
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
