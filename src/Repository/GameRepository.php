<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @return Game[]
     */
    public function getAll(): array
    {
        return $this->findAll();
    }


    public function getByProductName(string $productName): array
    {
        $record = $this->findBy(['productName'=>$productName]);
        if ($record=== null){
            throw new Exception();
        }
        return $record;
    }
///**
//* @return Game[] Returns an array of Game objects
//*/
//
//public function findByExampleField($value)
//{
//    return $this->createQueryBuilder('g')
//        ->andWhere('g.exampleField = :val')
//        ->setParameter('val', $value)
//        ->orderBy('g.id', 'ASC')
//        ->setMaxResults(10)
//        ->getQuery()
//        ->getResult()
//        ;
//}


    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
