<?php

declare(strict_types=1);

namespace App\Domain\Common;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

interface DomainRepository
{
    public function get(int $id): ?object;

    public function add(object $entity): void;

    public function remove(object $entity): void;

    public function getCollectionByCriteria(DomainCriteria ...$criterias): Collection;

    public function getIteratorByCriteria(DomainCriteria ...$criterias): iterable;

    public function getOneByCriteria(DomainCriteria ...$criterias): ?object;

    public function createQueryBuilder(string $alias = ''): QueryBuilder;

    public function getAll(): iterable;

    public function createQuery(string $dql): Query;
}
