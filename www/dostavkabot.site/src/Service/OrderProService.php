<?php

namespace App\Service;

use App\Entity\Order;

class OrderProService
{
    public function __construct(
        private OrderService $orderService,
        private MessageService $messageService,
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
