<?php

namespace App\Service;

use App\Entity\Hub\Hub;
use App\Entity\Hub\Log;
use App\Entity\User;
use App\Exception\Service\HubException;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HubService {

    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;

    public function __construct(ValidatorInterface $validator,EntityManagerInterface $entityManager,) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws RandomException
     * @throws HubException
     */
    public function create(array $data, User $user): Hub {

        $log = new Log();
        $log->setType('info');
        $log->setMessage('Hub created');

        $hub = new Hub();
        $hub->setName($data['name']);
        $hub->setPairCode($this->generatePairCode());
        $hub->setUser($user);
        $hub->setAccessToken($this->generateRandomKey());
        $hub->setMqttName($this->generateRandomKey());
        $hub->setMqttKey($this->generateRandomKey());
        $hub->AddLog($log);

        $errors = $this->validator->validate($hub);
        if (count($errors) > 0) {
            throw new HubException($errors[0]->getMessage());
        }

        $this->entityManager->persist($hub);
        $this->entityManager->flush();

        return $hub;
    }



    /**
     * @throws RandomException
     */
    private function generatePairCode(): int {
        return random_int(100000, 999999);
    }

    /**
     * @throws RandomException
     */
    private function generateRandomKey(): string {
        return hash('sha256', random_bytes(64));
    }

}