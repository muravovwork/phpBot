<?php

namespace App\Controller;

use App\Command\StartCommand;
use App\Service\ProBotService;
use App\Service\RequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use WeStacks\TeleBot\TeleBot;

class ProBotController extends AbstractController
{
    public function __construct(
        private RequestService $requestService,
        private ProBotService $botService,
    ) {
    }

    #[Route('/pro', name: 'app_pro_bot', methods: ['POST'])]
    public function index(): JsonResponse
    {
        file_put_contents('file.txt', print_r(file_get_contents('php://input', 1)."\n", FILE_APPEND));
        $data = json_decode(file_get_contents('php://input'), true);
        $dto = $this->requestService->createDto($data);
        $this->botService->exec($dto);

        return new JsonResponse([]);
    }
}
