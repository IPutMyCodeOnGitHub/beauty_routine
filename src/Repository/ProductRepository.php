<?php

namespace App\Repository;

use App\Entity\Product;
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

    public function getQueryBuilderSearchProducts(?ProductType $type, ?string $productName): QueryBuilder
    {
        $query = $this->createQueryBuilder('p');
        $query->orderBy('p.id', 'ASC');

        if ($type) {
            $query->andWhere('p.type = :type')
                ->setParameter('type', $type);
        }

        if ($productName) {
            $query->andWhere(
                $query->expr()
                    ->like('p.name',':name')
            )
                ->setParameter('name', '%'. $productName . '%');
        }

        return $query;
    }

    public function searchProductPaginator(
        ?ProductType $type,
        ?string $productName,
        int $page = 1,
        int $countObj = 10): ?PaginationInterface
    {
        $queryBuilder = $this->getQueryBuilderSearchProducts($type, $productName);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $countObj
        );
    }
}
