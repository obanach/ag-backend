<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends BaseController {
    #[Route(path: '/', name: 'main')]
    public function index(): Response {

        return $this->blankView([
            'name' => 'AutoGrow API',
            'version' => '1.0',
        ]);
    }
}
