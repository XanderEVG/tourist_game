<?php

namespace App\Repository;

use App\Entity\Marker;
use App\Entity\User;
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
 * @method Marker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Marker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Marker[]    findAll()
 * @method Marker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarkerRepository extends ServiceEntityRepository implements FindWithFilterAndSort
{
    use FindWithSort;
    use FindWithFilter;

    private string $alias = 'm';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marker::class);
    }

private function columnMaps(): array
    {
        return array(
            'difficulty' => 'd.name',
            'createdBy' => 'u.username',
        );
    }

    public function findWithSortAndFilters(array $filterBy, array $orderBy, $limit = 10, $offset = 0)
    {
        $alias = $this->alias;

        $queryBuilder = $this->createQueryBuilder($alias);
        $queryBuilder->select()
            ->leftJoin(User::class, 'u', 'WITH', 'u.id = m.createdBy')
            ->leftJoin(Difficulty::class, 'd', 'WITH', 'd.id = m.difficulty');
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
        $queryBuilder->select("count($alias.id)")
            ->leftJoin(User::class, 'u', 'WITH', 'u.id = m.createdBy')
            ->leftJoin(Difficulty::class, 'd', 'WITH', 'd.id = m.difficulty');
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
            ->delete(Marker::class, $alias)
            ->where($queryBuilder->expr()->in("$alias.id", ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
    }



    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Marker $entity, bool $flush = true): void
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
    public function remove(Marker $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Marker[] Returns an array of Marker objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Marker
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
