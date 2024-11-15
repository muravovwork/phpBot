<?php

namespace App\Service;

use App\Dto\MessageDto;

class ProResponseService
{
    public function __construct(
        private MessageService $messageService,
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
