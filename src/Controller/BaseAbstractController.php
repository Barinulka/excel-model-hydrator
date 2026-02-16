<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseAbstractController extends AbstractController
{
    // Исходный массив, в нем содержится вся основная информация по открытой страничке
    public array $data = [];

    public function __construct(
        protected ManagerRegistry $doctrine,
    ) {
    
    }

    protected function setPageData($entity, array $arguments = []): void
    {
        $orm = $this->doctrine
            ->getRepository($entity)
            ->getPageData($arguments);

        $this->data['page'] = $orm;
    }   
}