<?php

namespace App\Service;

use App\Dto\MessageDto;

class BotService
{
    public function __construct(
        private ResponseService $responseService,
    ) {
    }

    public function exec(MessageDto $dto): void
    {
        if (RequestService::MESSAGE_TYPE === $dto->type) {
            $this->responseService->messageResponse($dto);
        }

        if (RequestService::CALLBACK_TYPE === $dto->type) {
            $this->responseService->callbackResponse($dto);
        }
    }
    
}
