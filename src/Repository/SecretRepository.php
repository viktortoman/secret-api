<?php

namespace App\Repository;

use App\Entity\Secret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Secret>
 *
 * @method Secret|null find($id, $lockMode = null, $lockVersion = null)
 * @method Secret|null findOneBy(array $criteria, array $orderBy = null)
 * @method Secret[]    findAll()
 * @method Secret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secret::class);
    }

    public function save(Secret $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Secret $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function toArray(Secret $entity): array
    {
        return [
            "hash" => $entity->getHash(),
            "secretText" => $entity->getSecretText(),
            "createdAt" => $entity->getCreatedAt(),
            "expiresAt" => $entity->getExpiresAt() ?? null,
            "remainingViews" => $entity->getRemainingViews()
        ];
    }

    public function collection(): array
    {
        $result = [];

        foreach($this->getAll() ?? [] as $item) {
            $result[] = $this->toArray($item);
        }

        return $result;
    }

    /**
     * @return Secret[] Returns an array of Secret objects
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByHash(string $hash): ?Secret
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.hash = :hash')
            ->andWhere('s.expiresAt > :now')
            ->andWhere('s.remainingViews > 0')
            ->setParameter('hash', $hash)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
