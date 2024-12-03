<?php

namespace App\Controller\Admin;

use App\Entity\ItemMenu;
use App\Controller\Admin\ItemMenuCrudController;
use App\Entity\Order;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(ItemMenuCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Админ-панель');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Меню', 'fas fa-bars', ItemMenu::class);
        yield MenuItem::linkToCrud('Заказы', 'fas fa-cart-plus', Order::class);
        yield MenuItem::linkToCrud('Пользователи', 'fas fa-address-book', User::class);
    }
}
