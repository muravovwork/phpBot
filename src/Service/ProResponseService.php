<?php

namespace App\Service;

use App\Dto\MessageDto;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use WeStacks\TeleBot\TeleBot;

class ProResponseService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageService $messageService,
        private OrderService $orderService,
        private MenuService $menuService,
    ) {
    }

    public function callbackResponse(MessageDto $dto): void
    {

        $this->messageService->createProBotMessage(
            $dto->chatId,
            '12332',
            'Показать меню',
            '/menu',
        );
    }

    public function messageResponse(MessageDto $dto): void
    {
        $this->messageService->createProBotMessage(
            $dto->chatId,
            '123123',
            'Показать меню',
            '/menu',
        );
    }
}
