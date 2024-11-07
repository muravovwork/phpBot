<?php

namespace App\Controller;

use App\Command\StartCommand;
use App\Service\BotService;
use App\Service\RequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use WeStacks\TeleBot\TeleBot;

class BotController extends AbstractController
{
    public function __construct(
        private RequestService $requestService,
        private BotService $botService,
    ) {
    }

    #[Route('/', name: 'app_bot')]
    public function index(): JsonResponse
    {
        file_put_contents('file.txt', print_r(file_get_contents('php://input', 1)."\n", FILE_APPEND));
        $data = json_decode(file_get_contents('php://input'), true);
        $dto = $this->requestService->createDto($data);
        $this->botService->exec($dto);

        return new JsonResponse([]);
    }
}
