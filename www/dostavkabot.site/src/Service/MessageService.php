<?php

namespace App\Service;

use App\Command\StartCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WeStacks\TeleBot\TeleBot;

class MessageService
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {
    }

    public function createMessage(string $chatId, string $text, ?string $buttonText = null, ?string $buttonCallback = null): void
    {
        $bot = new TeleBot(['token' => $this->parameterBag->get('bot_token')]);

        $message = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($buttonText && $buttonCallback) {
            $button = [
                'reply_markup' => [
                    'inline_keyboard' => [[[
                        'text' => $buttonText,
                        'callback_data' => $buttonCallback,
                    ]]],
                ],
            ];
            $message = array_merge($message, $button);
        }

        $bot->sendMessage($message);
    }

    public function createProBotMessage(string $chatId, string $text, ?string $buttonText = null, ?string $buttonCallback = null): void
    {
        $bot = new TeleBot(['token' => $this->parameterBag->get('pro_bot_token')]);

        $message = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($buttonText && $buttonCallback) {
            $button = [
                'reply_markup' => [
                    'inline_keyboard' => [[[
                        'text' => $buttonText,
                        'callback_data' => $buttonCallback,
                    ]]],
                ],
            ];
            $message = array_merge($message, $button);
        }

        $bot->sendMessage($message);
    }

    public function createProBotGroupMessage(string $text, ?string $buttonText = null, ?string $buttonCallback = null): void
    {
        $bot = new TeleBot(['token' => $this->parameterBag->get('pro_bot_token')]);

        $message = [
            'chat_id' => '-1002470986139',
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($buttonText && $buttonCallback) {
            $button = [
                'reply_markup' => [
                    'inline_keyboard' => [[[
                        'text' => $buttonText,
                        'callback_data' => $buttonCallback,
                    ]]],
                ],
            ];
            $message = array_merge($message, $button);
        }

        $bot->sendMessage($message);
    }
}
