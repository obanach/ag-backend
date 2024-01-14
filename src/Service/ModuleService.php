<?php

namespace App\Service;

use App\Entity\Hub\Module\Data;
use App\Entity\Hub\Module\Module;
use App\Exception\Service\ModuleException;
use App\Repository\Hub\HubRepository;
use App\Repository\Hub\Module\ModuleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ModuleService {

    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private ModuleRepository $moduleRepository;
    private HubRepository $hubRepository;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager, ModuleRepository $moduleRepository, HubRepository $hubRepository) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->moduleRepository = $moduleRepository;
        $this->hubRepository = $hubRepository;
    }

    /**
     * @throws ModuleException
     */
    public function create(int $hubId, array $data): Module {

        $hub = $this->hubRepository->findOneBy(['id' => $hubId]);
        if (!$hub) {
            throw new ModuleException("Hub not found");
        }

        $module = new Module();
        $module->setName($data['name']);
        $module->setType($data['type']);
        $module->setMacAddress($data['macAddress']);
        $module->setHub($hub);

        $this->entityValidator($module);
        $this->entityManager->persist($module);
        $this->entityManager->flush();

        return $module;
    }

    /**
     * @throws ModuleException
     */
    private function entityValidator($entity): void {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            throw new ModuleException($errors[0]->getMessage());
        }
    }

    /**
     * @throws ModuleException
     */
    public function get(int $hubId, int $moduleId): Module {

        $module = $this->moduleRepository->findOneBy(['id' => $moduleId]);
        if (!$module) {
            throw new ModuleException("Module not found");
        }

        if ($module->getHub()->getId() !== $hubId) {
            throw new ModuleException("Module assigned to another hub");
        }

        return $module;
    }

    /**
     * @throws ModuleException
     */
    public function update(int $hubId, int $moduleId, array $data): Module {
        $module = $this->moduleRepository->findOneBy(['id' => $moduleId]);

        if (!$module) {
            throw new ModuleException("Module not found");
        }

        if ($module->getHub()->getId() !== $hubId) {
            throw new ModuleException("Module assigned to another hub");
        }

        if (isset($data['name'])) {
            $module->setName($data['name']);
        }

        $this->entityValidator($module);
        $this->entityManager->persist($module);
        $this->entityManager->flush();

        return $module;
    }

    /**
     * @throws ModuleException
     */
    public function delete(int $hubId, int $moduleId): void {
        $module = $this->moduleRepository->findOneBy(['id' => $moduleId]);

        if (!$module) {
            throw new ModuleException("Module not found");
        }

        if ($module->getHub()->getId() !== $hubId) {
            throw new ModuleException("Module assigned to another hub");
        }

        $this->entityManager->remove($module);
        $this->entityManager->flush();
    }

    /**
     * @throws ModuleException
     */
    public function ping(int $hubId, int $moduleId): void {

        $module = $this->moduleRepository->findOneBy(['id' => $moduleId]);
        if (!$module) {
            throw new ModuleException("Module not found");
        }

        if ($module->getHub()->getId() !== $hubId) {
            throw new ModuleException("Module assigned to another hub");
        }

        $module->setPingAt(new DateTimeImmutable());
        $this->entityManager->persist($module);
        $this->entityManager->flush();
    }

    /**
     * @throws ModuleException
     */
    public function createData(int $hubId, int $moduleId, array $data): Data {
        $module = $this->moduleRepository->findOneBy(['id' => $moduleId]);
        if (!$module) {
            throw new ModuleException("Module not found");
        }

        if ($module->getHub()->getId() !== $hubId) {
            throw new ModuleException("Module assigned to another hub");
        }


        switch ($module->getType()):
            case 'environment':
                if (!isset($data['temperature']) || !isset($data['humidity']) || !isset($data['dirt']) || !isset($data['battery'])) {
                    throw new ModuleException("Missing required data keys");
                }
                break;
            case 'switch':
                if (!isset($data['state'])) {
                    throw new ModuleException("Missing required data keys");
                }
                break;
            default:
                throw new ModuleException("Unknown module type");
        endswitch;


        $moduleData = new Data();
        $moduleData->setData($data);
        $moduleData->setModule($module);

        $this->entityManager->persist($moduleData);
        $this->entityManager->flush();

        return $moduleData;
    }

}