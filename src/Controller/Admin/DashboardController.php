<?php

namespace App\Controller\Admin;

use App\Admin\Module\AdminModuleProvider;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
final class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly AdminModuleProvider $adminModuleProvider
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'modules' => $this->adminModuleProvider->getDashboardModules(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Excel Model Hydrator')
            ->renderContentMaximized();
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addCssFile('styles/admin/dashboard.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('На сайт', 'fa fa-globe', '/')->setLinkTarget('_blank');
        yield MenuItem::section('&nbsp;');
        yield MenuItem::linkToDashboard('Главная', 'fa fa-home');
        yield MenuItem::section('Модули');
        yield from $this->adminModuleProvider->getMenuItems();
    }
}
