<?php

namespace App\Controller\Api\Secure;

use App\Entity\Jaime;
use App\Service\Service;
use App\Shared\Messages;
use App\Shared\Reponses;
use App\Service\Repository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'ctrl_user')]
#[Security("is_granted('ROLE_USER')")]
class UserController extends AbstractController
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

    #[Route('/jaime', name: 'ajouteJaime', methods: 'POST')]
    public function ajouteJaime():Response
    {
        $data=$this->service->json_decode();
        if(!isset($data->idUser) || $data->idUser===""){
            return $this->reponses->error(Messages::FORM_INVALID);
        }
        $user=$this->repository->userRepository()->findOneById($data->idUser);
        $jaime= new Jaime();
        $jaime->setJaime(1);
        $jaime->setUser($user);
        $this->service->em()->persist($jaime);
        $this->service->em()->flush();
        return $this->reponses->success($jaime->tojson(),1,Messages::JAIME);
    }


}