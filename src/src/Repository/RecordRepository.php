<?php

namespace App\Repository;

use App\Entity\Record;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[]    findAll()
 * @method Record[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    /**
     * Liste des nouveautés (albums sortis il y'a moins d'un mois)
     * @return Record[]
     */
    public function getNews(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.releasedAt > :last_month')
            ->setParameter('last_month', new \DateTime('-1 month'))
            ->orderBy('r.releasedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
