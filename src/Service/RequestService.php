<?php

namespace App\Service;

use App\Service\StartService;
use App\Dto\MessageDto;

class RequestService
{
    public const MESSAGE_TYPE = 'message';
    public const CALLBACK_TYPE = 'callback';

    public function __construct(
    ) {
    }

    public function catchWebhook(): array
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function createDto(array $data): ?MessageDto
    {
        $dto = new MessageDto();

        if (isset($data['callback_query'])) {
            $dto->text = $data['callback_query']['data'];
            $dto->chatId = $data['callback_query']['message']['chat']['id'];
            $dto->messageId = $data['callback_query']['message']['message_id'];
            $dto->telegramId = $data['callback_query']['from']['id'];
            $dto->firstName = $data['callback_query']['from']['first_name'];
            $dto->lastName = $data['callback_query']['from']['last_name'] ?? '';
            $dto->type = self::CALLBACK_TYPE;
        }

        if (isset($data['message'])) {
            $dto->text = $data['message']['text'] ?? '';
            $dto->chatId = $data['message']['chat']['id'];
            $dto->telegramId = $data['message']['from']['id'];
            $dto->firstName = $data['message']['from']['first_name'];
            $dto->lastName = $data['message']['from']['last_name'] ?? '';
            $dto->phone = $data['message']['contact']['phone_number'] ?? null;
            $dto->type = self::MESSAGE_TYPE;
        }

        return $dto;
    }
}
