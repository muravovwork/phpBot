<?php

namespace App\Service;

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
use WeStacks\TeleBot\TeleBot;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository,
        private UserRepository $userRepository,
        private OrderItemRepository $orderItemRepository,
        private ItemMenuRepository $itemMenuRepository,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function addToOrder(MessageDto $dto): Order
    {
        $order = $this->getOrder($dto);

        $item = $this->itemMenuRepository->findOneBy(['command' => $dto->text]);

        $orderItem = $this->orderItemRepository->findOneBy(['itemMenu' => (string) $item->getId(), 'order' => (string) $order->getId()]);

        if ($orderItem) {
            $orderItem->setCount($orderItem->getCount() + 1);
        } else {
            $orderItem = new OrderItem($order, $item);
        }
        $this->entityManager->persist($orderItem);

        $order->setStatus(Order::STATUS_IN_FILL_BY_CLIENT);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function getOrder(MessageDto $dto): Order
    {
        $user = $this->getTelegramUser($dto);
        $order = $this->orderRepository->getActiveOrder($user);

        if (!$order) {
            $order = new Order($user);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        }

        return $order;
    }

    public function getTelegramUser(MessageDto $dto): User
    {
        $user = $this->userRepository->findOneBy(['telegramId' => $dto->telegramId]);

        if (!$user) {
            $user = (new User())
                ->setTelegramId($dto->telegramId)
                ->setFirstName($dto->firstName)
                ->setLastName($dto->lastName)
            ;
        }

        $user
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
        ;
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function getOrderItems(Order $order): array
    {
        $orderItems = $this->orderItemRepository->findBy(['order' => (string) $order->getId()]);

        return $orderItems;
    }

    public function sendItems(Order $order, MessageDto $dto): void
    {
        $bot = new TeleBot(['token' => $this->parameterBag->get('bot_token')]);

        $orderItems = $this->getOrderItems($order);
        $textOrder = "";
        $price = 0;
        foreach ($orderItems as $item) {
            $price += $item->getItemMenu()->getPrice() * $item->getCount();
            $textOrder = $textOrder.' '.sprintf("\n%s [х%s]", $item->getItemMenu()->getName(), $item->getCount());
        }

        $inlineKeyboard = [
            [[
                'text' => 'Показать меню',
                'callback_data' => '/menu',
            ]],

            [[
                'text' => 'Очистить корзину',
                'callback_data' => '/clear',
            ]],
        ];

        if ($price >= 1000) {
            $text = sprintf("Продолжить заказ или перейти к оформлению?\n<b>Состав корзины</b>:%s\n<b>Общая стоимость</b>: %s р.", $textOrder, $price);
            $inlineKeyboard[] = [[
                'text' => 'Перейти к оформлению',
                'callback_data' => '/date',
            ]];
        } else {
            $text = sprintf("Для оформления заказа добавьте в корзину товаров от 1000р.\n<b>Состав корзины</b>:%s \n<b>Общая стоимость</b>: <b>%s</b> р.", $textOrder, $price);
        }

        $message = [
            'chat_id' => $dto->chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => [
                'inline_keyboard' => $inlineKeyboard
            ],
        ];

        $bot->sendMessage($message);
    }

    public function removeOrder(Order $order)
    {
        $orderItems = $this->getOrderItems($order);
        foreach ($orderItems as $item) {
            $this->entityManager->remove($item);
        }

        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }

    public function sendDate(MessageDto $dto): void
    {
        $bot = new TeleBot(['token' => $this->parameterBag->get('bot_token')]);

        $dateNow = new \DateTime('+ 1 day');

        $date = [];
        for ($i = 0; $i < 7; ++$i) {
            $date[] = [[
                'text' => $dateNow->format('d.m.Y'),
                'callback_data' => sprintf('/select%s', $dateNow->format('d.m.Y')),
            ]];
            $dateNow->modify('+1 day');
        }

        $message = [
            'chat_id' => $dto->chatId,
            'text' => 'Выберите дату доставки:',
            'reply_markup' => [
                'inline_keyboard' => $date,
            ],
        ];

        $bot->sendMessage($message);
    }

    public function sendOrder(Order $order, MessageDto $dto): void
    {
        $bot = new TeleBot(['token' => $this->parameterBag->get('bot_token')]);

        $orderItems = $this->getOrderItems($order);
        $text = "";
        $price = 0;
        foreach ($orderItems as $item) {
            $price += $item->getItemMenu()->getPrice() * $item->getCount();
            $text = $text.' '.sprintf("\n%s [х%s]", $item->getItemMenu()->getName(), $item->getCount());
        }

        $inlineKeyboard = [
            [[
                'text' => 'Подтвердить заказ',
                'callback_data' => '/confirm',
            ]],

            [[
                'text' => 'Отменить заказ',
                'callback_data' => '/clear',
            ]],
        ];

        $text = sprintf("Заказ <b>№%s</b>\nСостав: %s.\nАдрес доставки: %s.\nДата доставки: %s.\nКонтакт для связи: %s.\nОбщая стоимость: %sр",
            $order->getId(),
            $text,
            $order->getAddress(),
            $order->getDate(),
            $order->getUser()->getPhone() ?? '',
            $price
        );

        $message = [
            'chat_id' => $dto->chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => [
                'inline_keyboard' => $inlineKeyboard
            ],
        ];

        $bot->sendMessage($message);
    }

    public function addDate(MessageDto $dto): void
    {
        $date = substr($dto->text, strlen('/select'));
        $order = $this->getOrder($dto);
        $order->setDate($date);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function addAddress(MessageDto $dto): void
    {
        $order = $this->getOrder($dto);
        $order->setAddress($dto->text);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
