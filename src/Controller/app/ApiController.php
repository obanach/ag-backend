<?php

namespace App\Controller\app;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(): JsonResponse {
        return $this->json([
            'name' => 'autogrow-api',
            'version' => '1.0',
        ]);
    }
}
