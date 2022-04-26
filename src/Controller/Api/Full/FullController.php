<?php
namespace App\Controller\Api\Full;

use App\Entity\Jaime;
use App\Entity\User;
use App\Service\Service;
use App\Shared\Messages;
use App\Shared\Reponses;
use App\Service\Repository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FullController extends AbstractController
{
    private Repository $repository;
    private Service $service;
    private Reponses $reponses;

    public function __construct(Repository $repository,Service $service,Reponses $reponses)
    {
        $this->repository=$repository;
        $this->service=$service;
        $this->reponses=$reponses;
    }

    #[Route('/afficherUser', name: 'afficherUser', methods: 'GET')]
    public function afficherUser(): Response
    {
        $users=$this->repository->userRepository()->findAll();
        return $this->reponses->success( array_map(function (User $user) {
            return $user->tojson();
        }, $users),count($users),Messages::SUCCESS);
    }

    #[Route('/jaime', name: 'afficheJaime', methods: 'GET')]
    public function afficheJaime():Response
    {
       $jaimes=$this->repository->JaimeRepository()->findAll();
       return $this->reponses->success( array_map(function (Jaime $jaime) {
        return $jaime->tojson();
    }, $jaimes),count($jaimes),Messages::SUCCESS);
    }
}