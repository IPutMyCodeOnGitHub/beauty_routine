<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Paginator;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private $paginator;
    public function __construct(ManagerRegistry $registry, Paginator  $paginator)
    {
        parent::__construct($registry, User::class);
        $this->paginator = $paginator;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getQueryBuilderSearchExpert(?string $search, ?bool $unaproved): QueryBuilder
    {
        $query = $this->createQueryBuilder('u')
            ->orderBy('u.email', 'ASC');

        if ($search) {
            $query->orWhere($query->expr()->like('u.name', ':search'))
                ->orWhere($query->expr()->like('u.email', ':search'))
                ->setParameter('search', '%'.$search.'%');
        }

        if ($unaproved) {
            $query->andwhere($query->expr()->like('u.roles', ':role'))
                ->setParameter('role', '%'.User::ROLE_USER.'%')
                ->join('App\Entity\UserCertificate', 'cert')
                ->andWhere('cert MEMBER OF u.userCertificates');
        } else {
            $query
                ->andWhere($query->expr()->like('u.roles', ':role'))
                ->setParameter('role', '%'.User::ROLE_EXPERT.'%')
                ->join('App\Entity\UserCertificate', 'cert')
                ->orWhere('cert MEMBER OF u.userCertificates')
                ;
        }

        return $query;
    }

    public function getQueryBuilderSearchUser(?string $search, ?bool $valid): QueryBuilder
    {
        $query = $this->createQueryBuilder('u');
        $query->andwhere($query->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%'.User::ROLE_USER.'%')
            ->andWhere('u.userCertificates IS EMPTY')
            ->orderBy('u.email', 'ASC');

        if ($search) {
            $query->andwhere($query->expr()->orX(
                $query->expr()->like('u.name', ':search'),
                $query->expr()->like('u.email',':search')
                ))
                ->setParameter('search', '%'.$search.'%');
        }
        
        if ($valid) {
            $query->andWhere($query->expr()->isNotNull('u.verifyCode'));
        }

        return $query;
    }

    public function getQueryBuilderSearchAdmin(?string $search): QueryBuilder
    {
        $query = $this->createQueryBuilder('u');
        $query->andwhere($query->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%'.User::ROLE_ADMIN.'%')
            ->orderBy('u.email', 'ASC');

        if ($search) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('u.name', ':search'),
                    $query->expr()->like('u.email',':search')))
                ->setParameter('search', '%'.$search.'%');
        }

        return $query;
    }

    public function findSearchExpertPaginator(?string $search, ?bool $active, int $page = 1,  int $countObj = 10)
    {
        $queryBuilder = $this->getQueryBuilderSearchExpert($search, $active);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $countObj
        );
    }

    public function findSearchUserPaginator(?string $search, ?bool $valid, int $page = 1,  int $countObj = 10)
    {
        $queryBuilder = $this->getQueryBuilderSearchUser($search, $valid);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $countObj
        );
    }

    public function findSearchAdminsPaginator(?string $search, int $page = 1,  int $countObj = 10)
    {
        $queryBuilder = $this->getQueryBuilderSearchAdmin($search);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $countObj
        );
    }
}
