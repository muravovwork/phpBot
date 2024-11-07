<?php

namespace App\Repository;

use App\Entity\ItemMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method ItemMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemMenu[]    findAll()
 * @method ItemMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemMenu::class);
    }
}
