<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class MainController extends BaseController {
    #[Rest\Get(path: '/', name: 'main')]
    public function index(): Response {

        return $this->blankView([
            'name' => 'AutoGrow API',
            'version' => '1.0',
        ]);
    }
}
