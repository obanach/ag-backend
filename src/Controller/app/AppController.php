<?php

namespace App\Controller\app;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

#[Rest\Route('/app', name: 'app_')]
class AppController extends BaseController {
    #[Rest\Get(path: '/', name: 'index')]
    public function index(): Response {

        return $this->blankView([
            'status' => true,
            'message' => 'Welcome to the API',
        ]);
    }
}
