<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @return Query
     */
    public function findAllVisibleQuery(PropertySearch $search): Query
    {
        $query = $this->findVisibleQuery();

        if ($search->getMaxPrice()) {
            $query = $query
                ->andWhere('p.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }

        if ($search->getMinSurface()) {
            $query = $query
                ->andWhere('p.surface >= :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }

        if ($search->getOptions()->count() > 0) {
            $k = 0;
            foreach ($search->getOptions() as $option) {
                $k++;
                $query = $query
                    ->andWhere(":option$k MEMBER OF p.options")
                    ->setParameter("option$k", $option);
            }
        }

        // Distance filter: the search radius is applied around the selected address
        if (null !== $search->getLat() && null !== $search->getLng() && $search->getDistance()) {
            $ids = $this->findIdsWithinDistance($search->getLat(), $search->getLng(), $search->getDistance());
            $query = $query
                ->andWhere('p.id IN (:ids)')
                ->setParameter('ids', $ids ?: [0]); // [0] makes the query return nothing when no property matches
        }

        return $query->getQuery();
    }



    /**
     * @return Property[] Returns an array of Property objects
     */
    public function findLatest(): array
    {
        return $this->findVisibleQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the ids of the properties located within $distance kilometers of the given point.
     *
     * @return int[]
     */
    private function findIdsWithinDistance(float $lat, float $lng, int $distance): array
    {
        // Spherical law of cosines, earth radius of 6371 km
        $sql = 'SELECT id FROM property
                WHERE lat IS NOT NULL AND lng IS NOT NULL
                AND 6371 * ACOS(LEAST(1, GREATEST(-1,
                    :cosLat * COS(RADIANS(lat)) * COS(RADIANS(lng) - :lngRad) + :sinLat * SIN(RADIANS(lat))
                ))) <= :distance';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'cosLat' => cos(deg2rad($lat)),
            'sinLat' => sin(deg2rad($lat)),
            'lngRad' => deg2rad($lng),
            'distance' => $distance,
        ])->fetchFirstColumn();
    }

    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.sold = false')
        ;
    }

//    /**
//     * @return Property[] Returns an array of Property objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Property
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
