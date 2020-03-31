<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Common\DomainCriteria;
use App\Domain\Common\DomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractDoctrineRepository implements DomainRepository
{
    protected EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository($this->repositoryClassName());

        $this->em = $em;
        $this->repo = $repo;
    }

    abstract public function repositoryClassName(): string;

    public function get(int $id): ?object
    {
        return $this->repo->find($id);
    }

    public function add(object $entity): void
    {
        $this->em->persist($entity);
    }

    public function remove(object $entity): void
    {
        $this->em->remove($entity);
    }

    public function getCollectionByCriteria(DomainCriteria ...$criterias): Collection
    {
        $qb = $this->repo->createQueryBuilder('alias');

        foreach ($criterias as $criteria) {
            $qb->addCriteria($criteria->create());
        }

        return new ArrayCollection(
            $qb->getQuery()->getResult()
        );
    }

    public function getIteratorByCriteria(DomainCriteria ...$criterias): iterable
    {
        $qb = $this->repo->createQueryBuilder('alias');

        foreach ($criterias as $criteria) {
            $qb->addCriteria($criteria->create());
        }

        return $qb->getQuery()->iterate();
    }

    public function getOneByCriteria(DomainCriteria ...$criterias): ?object
    {
        return $this->getCollectionByCriteria(...$criterias)->first() ?: null;
    }

    public function createQueryBuilder(string $alias = ''): QueryBuilder
    {
        return $this->repo->createQueryBuilder($alias);
    }

    public function getAll(): iterable
    {
        return $this->repo->findAll();
    }

    public function createQuery(string $dql): Query
    {
        return $this->em->createQuery($dql);
    }
}
