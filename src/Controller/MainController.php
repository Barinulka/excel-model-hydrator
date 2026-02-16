<?php

namespace App\Controller;

use App\Entity\Main;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends BaseAbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {   
        $this->setPageData(Main::class);

        return $this->render('main/index.html.twig', 
            [
                'data' => $this->data,
            ]
        );
    }
}
