<?php

namespace App\Services;

use App\Entity\Secret;
use App\Repository\SecretRepository;
use Doctrine\ORM\NonUniqueResultException;

class SecretService
{
    private SecretRepository $repository;

    public function __construct(SecretRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws \Exception
     */
    public function create(array $data): Secret
    {
        $secretText = $data['secret'];
        $expireAfter =  $data['expireAfter'];

        if ($expireAfter === 0) {
            $expiresAt = null;
        } else {
            $expiresAt = new \DateTimeImmutable();
            $expiresAt = $expiresAt->add(new \DateInterval('PT' . $expireAfter . 'M'));
        }

        $entity = new Secret();
        $entity->setHash(sha1($secretText));
        $entity->setSecretText($secretText);
        $entity->setExpiresAt($expiresAt);
        $entity->setRemainingViews($data['expireAfterViews']);

        $this->repository->save($entity);

        return $entity;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByHash(string $hash): ?Secret
    {
        $entity = $this->repository->findByHash($hash);

        if ($entity) {
            $entity->setRemainingViews($entity->getRemainingViews() - 1);
            $this->repository->save($entity);

            return $entity;
        }

        return null;
    }
}