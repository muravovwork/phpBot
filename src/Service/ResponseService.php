<?php

namespace App\Service;

use App\Dto\MessageDto;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WeStacks\TeleBot\TeleBot;

class ResponseService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageService $messageService,
        private OrderService $orderService,
        private MenuService $menuService,
        private StepService $stepService,
        private OrderProService $orderProService,
        private ParameterBagInterface $parameterBag,
        private UserRepository $userRepository,
    ) {
    }

    public function callbackResponse(MessageDto $dto): void
    {
        $cache = new FilesystemAdapter();
        $state = $cache->getItem($dto->telegramId);

        if ('/date' === $dto->text) {
            $this->stepService->checkCurrentStep($dto, '/start');
            $this->orderService->sendDate($dto);
            $state->set($dto->text);
            $cache->save($state);
        }

        if (false !== stripos($dto->text, '/select')) {
            $this->stepService->checkCurrentStep($dto, '/date');
            $this->orderService->addDate($dto);
            $this->menuService->sendMenu($dto);
            $state->set($dto->text);
            $cache->save($state);
        }

        if ('/menu' === $dto->text) {
            $this->menuService->sendMenu($dto);
            $state->set($dto->text);
            $cache->save($state);
        }

        if (false !== stripos($dto->text, '/order')) {
            $order = $this->orderService->addToOrder($dto);
            $this->orderService->sendItems($order, $dto);
            $state->set('/order');
            $cache->save($state);
        }


        if ('/address' === $dto->text) {
            $this->stepService->checkCurrentStep($dto, '/order');
            $this->messageService->createMessage($dto->chatId, sprintf('Введите адрес доставки: '));
            $state->set($dto->text);
            $cache->save($state);
        }

        if ('/confirm' === $dto->text) {

            $this->stepService->checkCurrentStep($dto, '/contact');
            $order = $this->orderService->getOrder($dto);

            //$this->orderProService->sendOrderToGroup($order);
            $order->setStatus(Order::STATUS_ON_CONFIRM);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $this->messageService->createMessage($dto->chatId, 'Заказ создан! ✅ В ближайшее время с вами свяжется наш оператор для подтверждения заказа!');
            $state->set($dto->text);
            $cache->save($state);
        }

        if ('/clear' === $dto->text) {
            $order = $this->orderService->getOrder($dto);
            $this->orderService->removeOrder($order, $dto);
            $this->messageService->createMessage($dto->chatId, 'Заказ отменен! ❌ Создать новый заказ?', 'Создать новый', '/date');
            $state->set('/start');
            $cache->save($state);
        }


    }

    public function messageResponse(MessageDto $dto): void
    {
        $cache = new FilesystemAdapter();
        $state = $cache->getItem($dto->telegramId);

        if ('/start' === $dto->text) {
            $this->messageService->createMessage(
                $dto->chatId,
                'Здравствуйте! Я – чат-бот службы доставки еды. Для оформления заказа у нас минимальная сумма составляет 1000 рублей. Чем могу помочь вам сегодня?',
                'Выбрать дату доставки',
                '/date',
            );
            $state->set('/start');
            $cache->save($state);
        }  else if ('/address' === $state->get()) {
            $this->orderService->addAddress($dto);
            $bot = new TeleBot(['token' => $this->parameterBag->get('bot_token')]);
            $bot->sendMessage([
                'chat_id' => $dto->chatId,
                'parse_mode' => 'HTML',
                'caption' => 'sad',
                'text' => 'Отправить номер телефона:',
                'reply_markup' => [
                    'keyboard' => [[[
                        'text' => 'Поделиться контактом',
                        'callback_data' => '/contact',
                        'request_contact' => true
                    ]]],
                ],
            ]);
            $state->set('/contact');
            $cache->save($state);

        } else if ('/contact' === $state->get()) {
            $user = $this->userRepository->findOneBy(['telegramId' => $dto->telegramId]);
            $phone = null;
            if ($dto->text !== '') {
                $phone = $dto->text;
            } else {
                $phone = $dto->phone;
                $user->setPhone($dto->phone);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
            $order = $this->orderService->getOrder($dto);
            $order->setDescription($phone);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $this->orderService->sendOrder($order, $dto);
            $cache->save($state);
        }
    }
}
