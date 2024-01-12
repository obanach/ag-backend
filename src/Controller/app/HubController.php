<?php

namespace App\Controller\app;

use App\Controller\BaseController;
use App\Exception\Service\HubException;
use App\Service\HubService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app/hub', name: 'app_hub_')]
class HubController extends BaseController {

    private HubService $hubService;

    public function __construct (HubService $hubService) {
        $this->hubService = $hubService;
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): Response {

        $data = $request->toArray();

        if (!isset($data['name']) ) {
            return $this->errorView("Missing parameters");
        }

        try {
            $hub = $this->hubService->create($data, $this->getUser());
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }


        return $this->successView([
            'id' => $hub->getId(),
            'name' => $hub->getName(),
            'pairCode' => $hub->getPairCode()
        ]);
    }

}