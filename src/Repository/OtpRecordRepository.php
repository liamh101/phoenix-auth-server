<?php

namespace App\Repository;

use App\Entity\OtpRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OtpRecord>
 */
class OtpRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OtpRecord::class);
    }

    /**
     * @return OtpRecord[]
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return OtpRecord[]
     */
    public function getAccountHashes(): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.syncHash')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $id
     * @return array<string,string>|null
     */
    public function getSingleAccountHash(int $id): ?array
    {
        try {
            $record =  $this->createQueryBuilder('o')
                ->select('o.id', 'o.syncHash', 'o.updatedAt')
                ->where('o.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();

            $record['updatedAt'] = $record['updatedAt']->format('U');

            return $record;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function save(OtpRecord $record, bool $flush = true): void
    {
        $this->getEntityManager()->persist($record);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return OtpRecord[] Returns an array of OtpRecord objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OtpRecord
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
