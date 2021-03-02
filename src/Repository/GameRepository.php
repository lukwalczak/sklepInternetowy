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
    public function getAll(): ?array
    {
        return $this->findAll();
    }


    public function getOneByProductName(string $productName): ?Game
    {
        $record = $this->findOneBy(['productName'=>$productName]);
        if ($record == null){
            return null;
        }
        return $record;
    }

    public function getByID(int $id): ?Game
    {
        $record= $this->findOneBy(['id'=>$id]);
        if($record===null){
            throw new Exception();
        }
        return $record;
    }

    public function addGame(Game $game): void
    {
        $this->getEntityManager()->persist($game);
        $this->getEntityManager()->flush();
    }

    public function removeGame(Game $game): void
    {
        $this->getEntityManager()->remove($game);
        $this->getEntityManager()->flush();
    }

    public function getByGenre(string $genre): ?array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->setParameter( 'p', $genre)
            ->select('g')
            ->from('App:Game','g')
            ->where('g.genre LIKE :p')
            ->getQuery()
            ->getResult();
    }

}
