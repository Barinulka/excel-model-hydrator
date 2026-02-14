<?php

namespace App\Admin\Module;

use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use App\Repository\MainRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

final readonly class AdminModuleProvider
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private UserRepository $userRepository,
        private MainRepository $mainRepository
    ) {
    }

    /**
     * @return list<array{
     *     title: string,
     *     description: string,
     *     icon: string,
     *     count: int,
     *     url: string
     * }>
     */
    public function getDashboardModules(): array
    {
        return [
            [
                'title' => 'Пользователи',
                'description' => 'Список пользователей и их роли в системе.',
                'icon' => 'fa fa-users',
                'count' => $this->userRepository->count([]),
                'url' => (clone $this->adminUrlGenerator)
                    ->setController(UserCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl(),
            ],
            [
                'title' => 'Главная страница',
                'description' => 'Контент для блока главной страницы сайта.',
                'icon' => 'fa fa-house',
                'count' => $this->mainRepository->count([]),
                'url' => (clone $this->adminUrlGenerator)
                    ->setRoute('admin_main_settings')
                    ->generateUrl(),
            ],
        ];
    }

    /**
     * @return list<MenuItemInterface>
     */
    public function getMenuItems(): array
    {
        return $this->buildMenuItems($this->getMenuModules());
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function getMenuModules(): array
    {
        return [
            [
                'type' => 'crud',
                'title' => 'Пользователи',
                'icon' => 'fa fa-users',
                'entityClass' => User::class,
            ],
            [
                'type' => 'route',
                'title' => 'Главная страница',
                'icon' => 'fa fa-house',
                'routeName' => 'admin_main_settings',
            ],
            [
                'type' => 'submenu',
                'title' => 'Настройки',
                'icon' => 'fa fa-gear',
                'items' => [
                    [
                        'type' => 'route',
                        'title' => 'Главная страница',
                        'icon' => 'fa fa-house',
                        'routeName' => 'admin_main_settings',
                        'routeParameters' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param list<array<string, mixed>> $modules
     *
     * @return list<MenuItemInterface>
     */
    private function buildMenuItems(array $modules): array
    {
        $menuItems = [];

        foreach ($modules as $module) {
            if ('crud' === $module['type']) {
                $menuItems[] = MenuItem::linkToCrud($module['title'], $module['icon'], $module['entityClass']);
                continue;
            }

            if ('route' === $module['type']) {
                $menuItems[] = MenuItem::linkToRoute(
                    $module['title'],
                    $module['icon'],
                    $module['routeName'],
                    $module['routeParameters'] ?? []
                );
                continue;
            }

            if ('submenu' === $module['type']) {
                $subItems = $this->buildMenuItems($module['items'] ?? []);
                $menuItems[] = MenuItem::subMenu($module['title'], $module['icon'])->setSubItems($subItems);
                continue;
            }

            throw new \LogicException(sprintf('Unsupported menu module type "%s".', $module['type']));
        }

        return $menuItems;
    }
}
