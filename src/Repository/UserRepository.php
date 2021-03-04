<?php

namespace App\Repository;

use App\Entity\User;
use App\Exceptions\UserNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
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

    /**
     * @param int $id
     * @return User|null
     */
    public function getUserByID(int $id): ?User
    {
        $user = $this->findOneBy(['id'=>$id]);
        if ($user===null){
            throw new UserNotFoundException();
        }
        return $user;
    }

    /**
     * @return User[]|null
     */
    public function getAllUsers(): ?array
    {
        return $this->findAll();
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        $user = $this->findOneBy(['email'=>$email]);
        if ($user===null){
            throw new UserNotFoundException();
        }
        return $user;
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function newUser(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user): void
    {
        try {
            $this->getEntityManager()->remove($user);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {

        }
    }

    /**
     * @param string $email
     * @return int
     */
    public function getUserID(string $email): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->setParameter('p',$email)
            ->select('u.id')
            ->from('App:User','u')
            ->where('u.email = :p')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     */
    public function updateUser(User $user): void
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        }
        catch (ORMException $e) {

        }

    }
}
