<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Game;
use App\Exceptions\GameNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * @param string $productName
     * @return Game
     */
    public function getOneByProductName(string $productName): Game
    {
        $record = $this->findOneBy(['productName'=>$productName]);
        if ($record === null) {
            throw new GameNotFoundException();
        }
        return $record;
    }

    /**
     * @param int $id
     * @return Game
     */
    public function getByID(int $id): Game
    {
        $game = $this->findOneBy(['id'=>$id]);
        if ($game===null){
            throw new GameNotFoundException($id);
        }
        return $game;
    }

    /**
     * @param Game $game
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addGame(Game $game): void
    {
        $this->getEntityManager()->persist($game);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Game $game
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeGame(Game $game): void
    {
        $this->getEntityManager()->remove($game);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $genre
     * @return array|null
     */
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

    /**
     * @param Game $game
     */
    public function updateGame(Game $game): void
    {
        $this->getEntityManager()->persist($game);
        $this->getEntityManager()->flush();
    }

}
