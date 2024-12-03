<?php

namespace App\Service;

use App\Dto\MessageDto;
use App\Repository\ItemMenuRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WeStacks\TeleBot\TeleBot;

class MenuService
{
    public function __construct(
        private ItemMenuRepository $itemMenuRepository,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function sendMenu(MessageDto $dto): void
    {
        $bot = new TeleBot(['token' => $this->parameterBag->get('bot_token')]);

        $date = substr($dto->text, strlen('/select'));
        $dateTime = new \DateTime($date);
        $dayNumber = $dateTime->format('N');

        $itemsMenu = $this->itemMenuRepository->findBy(['day' => $dayNumber]);

        foreach ($itemsMenu as $item) {
            $caption = sprintf($item->getDescription(), $item->getPrice());
            
            $bot->sendPhoto([
                'chat_id' => $dto->chatId,
                'parse_mode' => 'HTML',
                'caption' => $caption,
                'photo' => $item->getPhotoUrl(),
                'reply_markup' => [
                    'inline_keyboard' => [[[
                        'text' => 'Заказать',
                        'callback_data' => $item->getCommand(),
                    ]]],
                ],
            ]);
        }
    }
}
