<?php

namespace App\Repository;

use App\Entity\Entrega;
use App\Entity\Subida;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subida>
 *
 * @method Subida|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subida|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subida[]    findAll()
 * @method Subida[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubidaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subida::class);
    }

    public function save(Subida $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Subida $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findLatestSubidaByUserAndEntrega(User $user, Entrega $entrega)
{
    return $this->createQueryBuilder('s')
        ->andWhere('s.user = :user')
        ->andWhere('s.entrega = :entrega')
        ->orderBy('s.fecha_subida', 'DESC')
        ->setMaxResults(1)
        ->setParameter('user', $user)
        ->setParameter('entrega', $entrega)
        ->getQuery()
        ->getOneOrNullResult();
}


//    /**
//     * @return Subida[] Returns an array of Subida objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Subida
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
