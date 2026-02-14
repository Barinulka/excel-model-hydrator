<?php

namespace App\Controller\Admin\Main;

use App\Entity\Main;
use App\Repository\MainRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class MainSettingsController extends AbstractController
{
    #[Route('/admin/settings/main', name: 'admin_main_settings', methods: ['GET'])]
    public function index(
        MainRepository $mainRepository,
        EntityManagerInterface $entityManager,
        AdminUrlGenerator $adminUrlGenerator
    ): RedirectResponse {
        $main = $mainRepository->findOneBy([], ['id' => 'ASC']);

        if (null === $main) {
            $main = new Main();
            $main->setTitle('Главная страница');

            $entityManager->persist($main);
            $entityManager->flush();
        }

        $editUrl = (clone $adminUrlGenerator)
            ->setController(MainCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($main->getId())
            ->generateUrl();

        return $this->redirect($editUrl);
    }
}
