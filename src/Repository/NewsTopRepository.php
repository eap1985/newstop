<?php

namespace eap1985\NewsTopBundle\Repository;

use eap1985\NewsTopBundle\Entity\NewsTop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NewsTop|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsTop|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsTop[]    findAll()
 * @method NewsTop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsTopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsTop::class);
    }

    public function findNotArchived(int $id): NewsTop
    {
        return $this->findOneBy([
            'id' => $id,
            'archived' => false
        ]);
    }

    public function findBySlug($slug): NewsTop
    {
        return $this->findOneBy([
            'slug' => $slug,
            'archived' => false
        ]);
    }
    /**
     * @return NewsTop[]
     */
    public function findAllNotArchived(): array
    {
        return $this->findBy([
            'archived' => false,
        ]);
    }

    /**
     * @return NewsTop[]
     */
    public function findLatestNews(): array
    {
        return $this->findBy(array('archived' => false), array('createdAt' => 'DESC'), 5);
    }

}
