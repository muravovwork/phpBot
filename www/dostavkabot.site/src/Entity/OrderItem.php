<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Table(name: '`order_item`')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: ItemMenu::class)]
    #[ORM\JoinColumn(name: 'item_menu_id', referencedColumnName: 'id')]
    private ItemMenu $itemMenu;

    #[ORM\Column]
    private int $count;

    public function __construct(
        Order $order,
        ItemMenu $itemMenu,
    ) {
        $this->order = $order;
        $this->itemMenu = $itemMenu;
        $this->count = 1;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getItemMenu(): ItemMenu
    {
        return $this->itemMenu;
    }

    public function setItemMenu(ItemMenu $itemMenu): self
    {
        $this->itemMenu = $itemMenu;

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }
}
