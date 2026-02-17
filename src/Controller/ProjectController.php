<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(): Response
    {
        $projects = [
            [
                'id' => 1,
                'name' => 'Marketplace 2026',
                'description' => 'Финмодель онлайн-платформы с подпиской и рекламной монетизацией.',
                'updatedAt' => '16 февраля 2026',
                'scenariosCount' => 4,
                'period' => '24 месяца',
                'modelUrl' => '#',
            ],
            [
                'id' => 2,
                'name' => 'Производство EcoPack',
                'description' => 'Модель запуска линии биоразлагаемой упаковки с тремя сценариями загрузки.',
                'updatedAt' => '14 февраля 2026',
                'scenariosCount' => 3,
                'period' => '36 месяцев',
                'modelUrl' => '#',
            ],
            [
                'id' => 3,
                'name' => 'Retail Expansion',
                'description' => 'Расширение сети точек продаж с анализом сезонности и промо-кампаний.',
                'updatedAt' => '10 февраля 2026',
                'scenariosCount' => 5,
                'period' => '18 месяцев',
                'modelUrl' => '#',
            ],
        ];

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
