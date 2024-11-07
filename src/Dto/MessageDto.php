<?php

namespace App\Dto;

class MessageDto
{
    public string $chatId;
    public string $telegramId;
    public string $firstName;
    public string $lastName;
    public string $text;
    public string $messageId;
    public ?string $phone;
    public string $type;
}
