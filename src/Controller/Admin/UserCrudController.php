<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Пользователь')
            ->setEntityLabelInPlural('Пользователи')
            ->setEntityPermission('ROLE_ADMIN')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать пользователя')
            ->setPageTitle(Crud::PAGE_INDEX, 'Список пользователей');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield EmailField::new('email', 'Email');

        yield ChoiceField::new('roles', 'Роли')
            ->setChoices([
                'Пользователь' => 'ROLE_USER',
                'Администратор' => 'ROLE_ADMIN',
                'Супер-администратор' => 'ROLE_SUPER_ADMIN',
            ])
            ->allowMultipleChoices()
            ->hideOnIndex();

        return;

    }

    public function configureActions(Actions $actions): Actions
    {
        $actionObj = $actions;
        $actionObj->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE)
            ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN');

        $actionObj->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->setIcon('fa fa-pencil-square-o')->setLabel('Редактировать');
        });
        $actionObj->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
            return $action->setIcon('fa fa-check')->setLabel('Сохранить и продолжить редактирование');
        });
        $actionObj->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
            return $action->setIcon('fa fa-check')->setLabel('Сохранить изменения');
        });

        return $actionObj;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User && $this->isSelfDemotion($entityInstance)) {
            $entityManager->refresh($entityInstance);
            $this->addFlash('danger', 'Нельзя убрать у себя роль супер-администратора.');

            return;
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User && $this->isCurrentUser($entityInstance)) {
            $this->addFlash('danger', 'Нельзя удалить собственного администратора.');

            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    private function isSelfDemotion(User $editedUser): bool
    {
        if (!$this->isCurrentUser($editedUser)) {
            return false;
        }

        return !in_array('ROLE_SUPER_ADMIN', $editedUser->getRoles(), true);
    }

    private function isCurrentUser(User $editedUser): bool
    {
        $currentUser = $this->getUser();

        return $currentUser instanceof User
            && null !== $currentUser->getId()
            && $currentUser->getId() === $editedUser->getId();
    }
}
