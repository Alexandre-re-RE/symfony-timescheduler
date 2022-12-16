<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * find one with status and client
     *
     * @param mixed $id
     * @return Project
     */
    public function findOneWithStatusAndClient($id)
    {
        return $this->queryWithStatusAndClient()
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * find all with status and client
     *
     * @param mixed $id
     * @return Project[]
     */
    public function findAllWithStatusAndClient()
    {
        return $this->queryWithStatusAndClient()
            ->getQuery()
            ->getResult();
    }

    public function findAllWithTasks()
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'p_tasks', 't_user', 't_status', 'p_client', 'p_status')
            ->leftJoin('p.tasks', 'p_tasks')
            ->leftJoin('p.client', 'p_client')
            ->innerJoin('p.status', 'p_status')
            ->leftJoin('p_tasks.user', 't_user')
            ->leftJoin('p_tasks.status', 't_status')
            ->getQuery()
            ->getResult();
    }

    private function queryWithStatusAndClient()
    {
        return $this->createQueryBuilder('p')
            ->select('p', 's', 'c')
            ->innerJoin('p.status', 's')
            ->innerJoin('p.client', 'c');
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
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

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
