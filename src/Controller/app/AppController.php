<?php

namespace App\Controller\app;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app', name: 'app_')]
class AppController extends BaseController {
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): Response {

        return $this->blankView([
            'status' => true,
            'message' => 'Welcome to the API',
        ]);
    }
}
