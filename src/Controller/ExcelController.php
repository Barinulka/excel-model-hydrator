<?php

namespace App\Controller;

use App\Exception\ExcelValidator\ExcelValidatorException;
use App\Service\ExcelModelGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class ExcelController extends AbstractController
{
    /**
     * @throws ExcelValidatorException
     */
    #[Route('/excel', name: 'app_excel', methods: ['POST'])]
    public function excel(Request $request, ExcelModelGenerator $generator): JsonResponse
    {
        // ← в реальности: $data = $request->toArray();
//        $data = [
//            'Input' => [
//                'H161' => 2000,
//                'H162' => 6600,
//                'H163' => 2000,
//                'H164' => 6600,
//            ],
//        ];

        $data = $request->toArray();

        $result = $generator->generateFromRequest($data);

        return $this->json([
            'message' => 'Model generated',
            ...$result,
        ]);
    }

    #[Route('/excel/output/{filename}', name: 'download_excel')]
    public function download(string $filename, KernelInterface $kernel): BinaryFileResponse
    {
        $path = $kernel->getProjectDir() . '/excel/output/' . $filename;
        if (!file_exists($path)) {
            throw $this->createNotFoundException();
        }
        return new BinaryFileResponse($path);
    }
}
