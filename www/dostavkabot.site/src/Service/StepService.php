<?php

namespace App\Service;

use App\Dto\MessageDto;
use App\Repository\ItemMenuRepository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WeStacks\TeleBot\TeleBot;

class StepService
{
    public function __construct(
        private MessageService $messageService
    ) {
    }

    public function checkCurrentStep(MessageDto $dto, string $current): void
    {
        $cache = new FilesystemAdapter();
        $state = $cache->getItem($dto->telegramId);

        if ($state->get() !== $current) {
            $this->messageService->createMessage($dto->chatId, 'На данном этапе заказа функция не доступна!');
            die;
        }
    }
}
