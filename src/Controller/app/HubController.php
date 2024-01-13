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

    public function __construct(HubService $hubService) {
        $this->hubService = $hubService;
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): Response {

        $data = $request->toArray();
        if (!isset($data['name'])) {
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
            'pairCode' => $hub->getPairCode(),
            'modulesCount' => count($hub->getModules()),
            'pingAt' => $hub->getPingAt(),
            'createdAt' => $hub->getCreatedAt(),
            'updatedAt' => $hub->getUpdatedAt(),
            'online' => $hub->getPingAt() > new \DateTimeImmutable('-5 minutes'),
        ]);
    }

    #[Route('/', name: 'get', methods: ['GET'])]
    public function get(): Response {

        $hubs = $this->hubService->getUserHubs($this->getUser());

        $data = [];
        foreach ($hubs as $hub) {
            $data[] = [
                'id' => $hub->getId(),
                'name' => $hub->getName(),
                'pairCode' => $hub->getPairCode(),
                'modulesCount' => count($hub->getModules()),
                'pingAt' => $hub->getPingAt(),
                'createdAt' => $hub->getCreatedAt(),
                'updatedAt' => $hub->getUpdatedAt(),
                'online' => $hub->getPingAt() > new \DateTimeImmutable('-5 minutes'),
            ];
        }

        return $this->successView($data);
    }

    #[Route('/{id}', name: 'get_details', methods: ['GET'])]
    public function getDetails(int $id): Response {

        try {
            $hub = $this->hubService->getDetails($id, $this->getUser());
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->successView([
            'id' => $hub->getId(),
            'name' => $hub->getName(),
            'pairCode' => $hub->getPairCode(),
            'modulesCount' => count($hub->getModules()),
            'pingAt' => $hub->getPingAt(),
            'createdAt' => $hub->getCreatedAt(),
            'updatedAt' => $hub->getUpdatedAt(),
            'online' => $hub->getPingAt() > new \DateTimeImmutable('-5 minutes'),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response {
        try {
            $this->hubService->delete($id, $this->getUser());
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }
        return $this->successView([]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): Response {

        $data = $request->toArray();
        if (!isset($data['name'])) {
            return $this->errorView("Missing parameters");
        }

        try {
            $hub = $this->hubService->update($id, $data, $this->getUser());
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->successView([
            'id' => $hub->getId(),
            'name' => $hub->getName(),
            'pairCode' => $hub->getPairCode(),
            'modulesCount' => count($hub->getModules()),
            'pingAt' => $hub->getPingAt(),
            'createdAt' => $hub->getCreatedAt(),
            'updatedAt' => $hub->getUpdatedAt(),
            'online' => $hub->getPingAt() > new \DateTimeImmutable('-5 minutes'),
        ]);
    }


    #[Route('/{id}/module', name: 'get_module', methods: ['GET'])]
    public function get_module(int $id): Response {
        try {
            $hub = $this->hubService->getDetails($id, $this->getUser());
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }

        $data = [];
        foreach ($hub->getModules() as $module) {
            $data[] = [
                'id' => $module->getId(),
                'name' => $module->getName(),
                'type' => $module->getType(),
                'batteryLevel' => $module->getBatteryLevel(),
                'pingAt' => $module->getPingAt(),
            ];
        }

        return $this->successView($data);
    }

}