<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
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
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
    * @return User[] Returns an array of User objects
    *
    */
    public function findNewUsersBetween($initialDate, $finalDate)
    {
        $now = new \DateTime();
        $initialDate = $now->modify("-".$days."  day");

        return $this->createQueryBuilder('u')
            ->where("u.createdAt BETWEEN :initialDate AND :finalDate")
            ->setParameter("initialDate", $initialDate)
            /* ->andWhere('u.exampleField = :val') */
            ->setParameter('finalDate', $now)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $initialDate
     * @param $finalDate
     * @return float|int|mixed|string
     */
    public function findNewUsersFromTo($initialDate, $finalDate)
    {
        return $this->createQueryBuilder('u')
            ->where("u.createdAt > :initialDate")
            ->setParameter("initialDate", $initialDate)
            ->andWhere('u.createdAt < :finalDate')
            ->setParameter('finalDate', $finalDate)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return float|int|mixed|string
     *
     */
    public function findNewUsersFromToURL(\DateTime $start, \DateTime $end){

        return $this->createQueryBuilder('u')
            ->where("u.createdAt > :initialDate")
            ->setParameter("initialDate", $start)
            ->andWhere('u.createdAt < :finalDate')
            ->setParameter('finalDate', $end)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
