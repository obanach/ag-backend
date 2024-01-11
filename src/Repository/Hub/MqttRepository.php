<?php

namespace App\Repository\Hub;

use App\Entity\Hub\Mqtt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mqtt>
 *
 * @method Mqtt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mqtt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mqtt[]    findAll()
 * @method Mqtt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MqttRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mqtt::class);
    }

//    /**
//     * @return Mqtt[] Returns an array of Mqtt objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mqtt
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
