<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

abstract class DeviceController extends AbstractFOSRestController {

    protected function blankView(array $data = [], int $status = Response::HTTP_OK): Response {
        $view = View::create($data, $status);
        return $this->handleView($view);
    }

    protected function successView(array $data = [], int $status = Response::HTTP_OK): Response {
        $view = View::create($data, $status);
        return $this->handleView($view);
    }

    protected function errorView(string $message, int $status = Response::HTTP_BAD_REQUEST): Response {
        $view = View::create([
            'message' => $message,
        ], $status);
        return $this->handleView($view);
    }


}
