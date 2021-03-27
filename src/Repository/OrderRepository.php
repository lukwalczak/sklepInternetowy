<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function addOrder(Order $order): void
    {
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }

    public function getUserOrders(int $id): ?array
    {
        return $this->getEntityManager()
                ->createQueryBuilder()
                ->setParameter('p',$id)
                ->select('o.id orderID','u.id userID','g.productName','g.id gameID')
                ->from('App:Order','o')
                ->Join('o.user','u')
                ->join('o.games','g')
                ->where('u.id = :p')
                ->getQuery()
                ->getResult();
    }
}
