<?php

namespace App\Repository;

use App\Entity\OtpRecord;
use App\ValueObject\RepoResponse\OtpRecord\AccountHash;
use App\ValueObject\RepoResponse\OtpRecord\AccountManifest;
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
        /** @var OtpRecord[] $result */
        $result = $this->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }


    /**
     * @return AccountManifest[]
     */
    public function getAccountManifest(): array
    {
        /** @var array $result */
        $result = $this->createQueryBuilder('o')
            ->select('o.id', 'o.updatedAt')
            ->getQuery()
            ->getResult();

        return AccountManifest::hydrateMany($result);
    }

    public function getSingleAccountHash(int $id): ?AccountHash
    {
        try {
            /** @var array $record */
            $record =  $this->createQueryBuilder('o')
                ->select('o.id', 'o.syncHash', 'o.updatedAt')
                ->where('o.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();

            return new AccountHash(id: $record['id'], syncHash: $record['syncHash'], updatedAt: $record['updatedAt']);
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findExistingAccountHash(string $hash): ?AccountHash
    {
        try {
            $record = $this->createQueryBuilder('o')
                ->select('o.id', 'o.syncHash', 'o.updatedAt')
                ->where('o.syncHash = :hash')
                ->setParameter('hash', $hash)
                ->getQuery()
                ->getSingleResult();

            return new AccountHash(id: $record['id'], syncHash: $record['syncHash'], updatedAt: $record['updatedAt']);
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
