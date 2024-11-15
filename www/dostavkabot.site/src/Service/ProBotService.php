<?php

namespace App\Service;

use App\Dto\MessageDto;

class ProBotService
{
    public function __construct(
        private ProResponseService $proResponseService,
    ) {
    }

    public function exec(MessageDto $dto): void
    {
        if (RequestService::MESSAGE_TYPE === $dto->type) {
            $this->proResponseService->messageResponse($dto);
        }

        if (RequestService::CALLBACK_TYPE === $dto->type) {
            $this->proResponseService->callbackResponse($dto);
        }
    }

}
