<?php

namespace App\Repository;

use App\Entity\Difficulty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use App\Common\Repository\ExtendedFind\ColumnMapper;
use App\Common\Repository\ExtendedFind\FindWithFilter;
use App\Common\Repository\ExtendedFind\FindWithFilterAndSort;
use App\Common\Repository\ExtendedFind\FindWithSort;

/**
 * @method Difficulty|null find($id, $lockMode = null, $lockVersion = null)
 * @method Difficulty|null findOneBy(array $criteria, array $orderBy = null)
 * @method Difficulty[]    findAll()
 * @method Difficulty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DifficultyRepository extends ServiceEntityRepository implements FindWithFilterAndSort
{
    use FindWithSort;
    use FindWithFilter;

    private string $alias = 'd';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Difficulty::class);
    }

    private function columnMaps(): array
    {
        return array();
    }

    public function findWithSortAndFilters(array $filterBy, array $orderBy, $limit = 10, $offset = 0)
    {
        $alias = $this->alias;

        $queryBuilder = $this->createQueryBuilder($alias);
        $queryBuilder->select();
        $filterBy = ColumnMapper::mapColumns($filterBy, $this->columnMaps(), $alias);
        $orderBy = ColumnMapper::mapColumns($orderBy, $this->columnMaps(), $alias);
        $queryBuilder = $this->addFiltersToQuery($queryBuilder, $filterBy, []);
        $queryBuilder = $this->addSortToQuery($queryBuilder, $orderBy);
        $queryBuilder->setMaxResults($limit)->setFirstResult($offset);

        return $queryBuilder->getQuery()->getResult();
    }

    public function countWithFilters(array $filterBy)
    {
        $alias = $this->alias;

        $queryBuilder = $this->createQueryBuilder($alias);
        $queryBuilder->select("count($alias.id)");
        $filterBy = ColumnMapper::mapColumns($filterBy, $this->columnMaps(), $alias);
        $queryBuilder = $this->addFiltersToQuery($queryBuilder, $filterBy, []);
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Удаление по  ИД.
     *
     * @param array $ids ИД.
     *
     * @return void
     */
    public function deleteById(array $ids): void
    {
        $alias = $this->alias;

        $queryBuilder = $this->createQueryBuilder($alias);
        $queryBuilder
            ->delete(Difficulty::class, $alias)
            ->where($queryBuilder->expr()->in("$alias.id", ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Difficulty $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Difficulty $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    
}
