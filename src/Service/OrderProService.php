<?php

namespace App\Service;

use App\Command\StartCommand;
use App\Dto\MessageDto;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\ItemMenuRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WeStacks\TeleBot\Objects\Keyboard;
use WeStacks\TeleBot\TeleBot;

class OrderProService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository,
        private UserRepository $userRepository,
        private OrderService $orderService,
        private MessageService $messageService,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function sendOrderToGroup(Order $order): void
    {
       $orderItems = $this->orderService->getOrderItems($order);
        $text = "";
        $price = 0;
        foreach ($orderItems as $item) {
            $price += $item->getItemMenu()->getPrice() * $item->getCount();
            $text = $text.' '.sprintf("\n%s [х%s]", $item->getItemMenu()->getName(), $item->getCount());
        }

        $text = sprintf("Заказ <b>№%s</b>\nСостав: %s.\nАдрес доставки: %s.\nДата доставки: %s.\nКонтакт для связи: %s.\nИмя: %s %s.\nОбщая стоимость: %sр",
            $order->getId(),
            $text,
            $order->getAddress(),
            $order->getDate(),
            $order->getUser()->getPhone() ?? '',
            $order->getUser()->getFirstName() ?? '',
            $order->getUser()->getLastName() ?? '',
            $price
        );


        $this->messageService->createProBotGroupMessage($text);
    }
}
