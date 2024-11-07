<?php

namespace App\Repository;

use App\Entity\OtpRecord;
use App\Service\UserService;
use App\ValueObject\RepoResponse\OtpRecord\AccountHash;
use App\ValueObject\RepoResponse\OtpRecord\AccountManifest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OtpRecord>
 */
class OtpRecordRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private UserService $userService,
    ) {
        parent::__construct($registry, OtpRecord::class);
    }

    public function getSingleRecord(int $id): ?OtpRecord
    {
        /** @var OtpRecord $result */
        $result = $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->andWhere('o.user = :user')
            ->setParameter('id', $id)
            ->setParameter('user', $this->userService->getCurrentUser()->getId())
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @return OtpRecord[]
     */
    public function getAll(): array
    {
        /** @var OtpRecord[] $result */
        $result = $this->createQueryBuilder('o')
            ->where('o.user = :user')
            ->orderBy('o.id', 'DESC')
            ->setParameter('user', $this->userService->getCurrentUser()->getId())
            ->getQuery()
            ->getResult();

        return $result;
    }


    /**
     * @return AccountManifest[]
     */
    public function getAccountManifest(): array
    {
        /** @var array<int, array<string, int|\DateTimeInterface>> $result */
        $result = $this->createQueryBuilder('o')
            ->select('o.id', 'o.updatedAt')
            ->where('o.user = :user')
            ->orderBy('o.updatedAt', 'DESC')
            ->setParameter('user', $this->userService->getCurrentUser()->getId())
            ->getQuery()
            ->getResult();

        return AccountManifest::hydrateMany($result);
    }

    public function getSingleAccountHash(int $id): ?AccountHash
    {
        try {
            /** @var array<string, int|string|\DateTimeInterface> $record */
            $record =  $this->createQueryBuilder('o')
                ->select('o.id', 'o.syncHash', 'o.updatedAt')
                ->where('o.id = :id')
                ->andWhere('o.user = :user')
                ->setParameter('id', $id)
                ->setParameter('user', $this->userService->getCurrentUser()->getId())
                ->getQuery()
                ->getSingleResult();

            if (
                !is_int($record['id']) ||
                !is_string($record['syncHash']) ||
                !$record['updatedAt'] instanceof \DateTimeInterface
            ) {
                throw new \RuntimeException('Invalid data returned.');
            }

            return new AccountHash(id: $record['id'], syncHash: $record['syncHash'], updatedAt: $record['updatedAt']);
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findExistingAccountHash(string $hash): ?AccountHash
    {
        try {
            /** @var array<string, int|string|\DateTimeInterface> $record */
            $record = $this->createQueryBuilder('o')
                ->select('o.id', 'o.syncHash', 'o.updatedAt')
                ->where('o.syncHash = :hash')
                ->andWhere('o.user = :user')
                ->setParameter('hash', $hash)
                ->setParameter('user', $this->userService->getCurrentUser()->getId())
                ->getQuery()
                ->getSingleResult();

            if (
                !is_int($record['id']) ||
                !is_string($record['syncHash']) ||
                !$record['updatedAt'] instanceof \DateTimeInterface
            ) {
                throw new \RuntimeException('Invalid data returned.');
            }

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

    public function delete(OtpRecord $record, bool $flush = true): void
    {
        $this->getEntityManager()->remove($record);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @deprecated Security bypass for users. Use getSingleRecord instead
     */
    public function find(mixed $id, int|LockMode|null $lockMode = null, ?int $lockVersion = null): object|null
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * @deprecated Security bypass for users. Use getAll instead
     */
    public function findAll(): array
    {
        return parent::findAll();
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
