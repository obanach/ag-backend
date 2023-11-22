<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function index(): JsonResponse
    {
        return $this->json([
            'name' => 'autogrow-api',
            'version' => '1.0',
        ]);
    }
}
