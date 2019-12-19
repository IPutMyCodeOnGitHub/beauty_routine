<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductTag;
use App\Entity\ProductType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, Paginator $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }


    public function getQueryBuilderSearchProductForDay(?ProductType $type, ?string $name, ?array $tags): QueryBuilder
    {
        $query = $this->createQueryBuilder('p');
        $query->orderBy('p.id', 'ASC');

        if ($type) {
            $query->andWhere('p.type = :type')
                ->setParameter('type', $type);
        }
        if ($name) {
            $query->andWhere($query->expr()->orX(
                    $query->expr()->like('p.name', ':name'),
                    $query->expr()->like('p.brand', ':name')))
                ->setParameter('name', '%' . $name .'%');
        }
        if ($tags) {
            $query->innerJoin('p.tags', 't')
                ->andWhere(
                    $query->expr()->in('t.id', ':tags'))
                ->setParameter('tags', $tags);
        }
        return $query;
    }


    public function searchProductForDay(
        ?ProductType $type,
        ?string $name,
        ?array $tags,
        int $page = 1,
        int $countObj = 10)
    : ?PaginationInterface
    {
        $queryBuilder = $this->getQueryBuilderSearchProductForDay($type, $name, $tags);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $countObj
        );
    }

    public function selectAllBrands()
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.brand');

        return $query;
    }

    public function selectAllCountries()
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.country')
            ->orderBy('p.country', 'ASC');

        return $query;
    }
}
