<?php

namespace App\Controller\device\hub;

use App\Controller\device\DeviceController;
use App\Exception\Service\HubException;
use App\Exception\Service\ModuleException;
use App\Repository\Hub\HubRepository;
use App\Service\HubService;
use App\Service\ModuleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/device/hub', name: 'device_hub_module_')]
class ModuleController extends DeviceController {

    private HubService $hubService;
    private ModuleService $moduleService;

    public function __construct(HubService $hubService, ModuleService $moduleService) {
        $this->hubService = $hubService;
        $this->moduleService = $moduleService;

    }

    #[Route('/{hubId}/module', name: 'create', methods: ['POST'])]
    public function create(int $hubId, Request $request): Response {

        $accessToken = $request->headers->get('X-Device-Token');
        $data = $request->toArray();
        try {
            $this->hubService->checkHubAccess($hubId, $accessToken);
            $module = $this->moduleService->create($hubId, $data);
        } catch (HubException|ModuleException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->successView([
            'id' => $module->getId(),
            'name' => $module->getName(),
            'type' => $module->getType(),
            'pingAt' => $module->getPingAt(),
            'createdAt' => $module->getCreatedAt(),
            'updatedAt' => $module->getUpdatedAt(),
        ]);
    }

    #[Route('/{hubId}/module/{moduleId}', name: 'get', methods: ['GET'])]
    public function get(int $hubId, int $moduleId, Request $request): Response {

        $accessToken = $request->headers->get('X-Device-Token');
        try {
            $this->hubService->checkHubAccess($hubId, $accessToken);
            $module = $this->moduleService->get($hubId, $moduleId);
        } catch (HubException|ModuleException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->successView([
            'id' => $module->getId(),
            'name' => $module->getName(),
            'type' => $module->getType(),
            'pingAt' => $module->getPingAt(),
            'createdAt' => $module->getCreatedAt(),
            'updatedAt' => $module->getUpdatedAt(),
        ]);
    }

    #[Route('/{hubId}/module/{moduleId}', name: 'update', methods: ['PUT'])]
    public function update(int $hubId, int $moduleId, Request $request): Response {

        $accessToken = $request->headers->get('X-Device-Token');
        $data = $request->toArray();
        try {
            $this->hubService->checkHubAccess($hubId, $accessToken);
            $module = $this->moduleService->update($hubId, $moduleId, $data);
        } catch (HubException|ModuleException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->successView([
            'id' => $module->getId(),
            'name' => $module->getName(),
            'type' => $module->getType(),
            'pingAt' => $module->getPingAt(),
            'createdAt' => $module->getCreatedAt(),
            'updatedAt' => $module->getUpdatedAt(),
        ]);
    }

    #[Route('/{hubId}/module/{moduleId}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $hubId, int $moduleId, Request $request): Response {

        $accessToken = $request->headers->get('X-Device-Token');
        try {
            $this->hubService->checkHubAccess($hubId, $accessToken);
            $this->moduleService->delete($hubId, $moduleId);
        } catch (HubException|ModuleException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->blankView();
    }

    #[Route('/{hubId}/module/{moduleId}/ping', name: 'ping', methods: ['POST'])]
    public function ping(int $hubId, int $moduleId, Request $request): Response {

        $accessToken = $request->headers->get('X-Device-Token');
        try {
            $this->hubService->checkHubAccess($hubId, $accessToken);
            $this->moduleService->ping($hubId, $moduleId);
        } catch (HubException|ModuleException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->blankView();
    }

    #[Route('/{hubId}/module/{moduleId}/data', name: 'data_create', methods: ['POST'])]
    public function data_create(int $hubId, int $moduleId, Request $request): Response {

        $accessToken = $request->headers->get('X-Device-Token');
        $data = $request->toArray();
        try {
            $this->hubService->checkHubAccess($hubId, $accessToken);
            $data = $this->moduleService->createData($hubId, $moduleId, $data);
        } catch (HubException|ModuleException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->successView([
            'id' => $data->getId(),
            'data' => $data->getData(),
            'createdAt' => $data->getCreatedAt(),
        ]);
    }

}
