<?php
namespace App\Controller\Api\Secure;

use DateTime;
use App\Entity\Presence;
use App\Service\Service;
use App\Shared\Messages;
use App\Shared\Reponses;
use App\Service\Repository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'ctrl_presence')]
#[Security("is_granted('ROLE_USER')")]
class PresenceController extends AbstractController
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

    #[Route('/presence', name: 'ajoutePresence', methods: 'POST')]
    public function ajoutePresence():Response
    {
        $data=$this->service->json_decode();
        if(!isset($data->idUser,$data->idEmploye) || ($data->idUser==="" || $data->idEmploye==="")){
            return $this->reponses->error(Messages::FORM_INVALID);
        }

        $datetime = new DateTime();
        $anne = $datetime->format('Y');
        $mois = $datetime->format('M');
        $date = $datetime->format('d');
        $heure = $datetime->format('H:i:s');

        $presence=$this->repository->PresenceRepository()->findOneBy(['anne'=>$anne,'mois'=>$mois,'date'=>$date,'user'=>$data->idUser,'employe'=>$data->idEmploye]);
        if($presence){
            return $this->reponses->error(Messages::PRESENCE_EXIST);
        }
        $user=$this->repository->userRepository()->findOneById($data->idUser);
        $employe=$this->repository->EmployeRepository()->findOneById($data->idEmploye);


        $presences = new Presence();
        $presences->setAnne($anne);
        $presences->setMois($mois);
        $presences->setDate($date);
        $presences->setHeureEntre($heure);
        $presences->setUser($user);
        $presences->setEmploye($employe);

        $this->service->em()->persist($presences);
        $this->service->em()->flush();

        return $this->reponses->success($presences->tojson(),1,Messages::SUCCESS_PRESENCE);
    }

    #[Route('/sortie', name: 'sortiePresence', methods: 'POST')]
    public function sortiePresence(): Response
    {

        $data=$this->service->json_decode();
        if(!isset($data->idUser,$data->id) || ($data->idUser==="" || $data->id==="")){
            return $this->reponses->error(Messages::FORM_INVALID);
        }

        $presence=$this->repository->PresenceRepository()->findOneBy(['id'=>$data->id,'user'=>$data->idUser]);

        $datetime = new DateTime();
        $heure = $datetime->format('H:i:s');
        $presence->setHeureSortie($heure);
        $this->service->em()->persist($presence);
        $this->service->em()->flush();

        return $this->reponses->success($presence->tojson(),1,Messages::PRESENCE_SORTIE);
    }

    #[Route('/anne/{id}', name: 'anne', methods: 'GET')]
    public function anne($id): Response
    {
       $anne=$this->repository->PresenceRepository()->findByAnne($id);

       return $this->reponses->success(array_map(function (Presence $presence) {
        return $presence->tojson();
    }, $anne),count($anne),Messages::SUCCESS);
    }

    #[Route('/mois/{id}', name: 'mois', methods: 'GET')]
    public function mois($id): Response
    {
       $mois=$this->repository->PresenceRepository()->findByMois($id);

       return $this->reponses->success(array_map(function (Presence $presence) {
        return $presence->tojson();
    }, $mois),count($mois),Messages::SUCCESS);
    }

    
    #[Route('/date/{id}', name: 'date', methods: 'GET')]
    public function date($id): Response
    {
       $date=$this->repository->PresenceRepository()->findByDate($id);

       return $this->reponses->success(array_map(function (Presence $presence) {
        return $presence->tojson();
    }, $date),count($date),Messages::SUCCESS);
    }

    #[Route('/presence/{id}', name: 'affichePresence', methods: 'GET')]
    public function affichePresence($id): Response
    {
        $datetime = new DateTime();
        $anne = $datetime->format('Y');
        $mois = $datetime->format('M');
        $date = $datetime->format('d');

       $presences=$this->repository->PresenceRepository()->findBy(['anne'=>$anne,'mois'=>$mois,'date'=>$date,'user'=>$id]);

       return $this->reponses->success(array_map(function (Presence $presence) {
        return $presence->tojson();
    }, $presences),count($presences),Messages::SUCCESS);
    }

    #[Route('/presence/{id}/{anne}/{mois}/{date}', name: 'parametrePesence', methods: 'GET')]
    public function  parametrePesence($id,$anne,$mois,$date):Response
    {

       $presences=$this->repository->PresenceRepository()->findBy(['anne'=>$anne,'mois'=>$mois,'date'=>$date,'user'=>$id]);

       return $this->reponses->success(array_map(function (Presence $presence) {
        return $presence->tojson();
    }, $presences),count($presences),Messages::SUCCESS);
    }


#[Route('/absent/{id}', name: 'absent', methods: 'GET')]
    public function  absent($id):Response
    {
       $employe=$this->repository->EmployeRepository()->findByUser($id);
        $datetime = new DateTime();
        $anne = $datetime->format('Y');
        $mois = $datetime->format('M');
        $date = $datetime->format('d');
       $absent=[];
       foreach($employe as $resultat){
        $presences=$this->repository->PresenceRepository()->findBy(['anne'=>$anne,'mois'=>$mois,'date'=>$date,'user'=>$id,'employe'=>$resultat->getId()]);
        if(!$presences){
            $absent[]=[
                'nom'=>$resultat->getNom(),
                'prenom'=>$resultat->getPrenom(),
                'contact'=>$resultat->getContact(),
                'email'=>$resultat->getEmail(),
                'travaille'=>$resultat->getTravaille()
            ];
        }
      }
    return $this->reponses->success($absent,count($absent),Messages::SUCCESS);
    }


    #[Route('/absent/{id}/{anne}/{mois}/{date}', name: 'parametreAbsent', methods: 'GET')]
    public function  parametreAbsent($id,$anne,$mois,$date):Response
    {
       $employe=$this->repository->EmployeRepository()->findByUser($id);

       $absent=[];
       foreach($employe as $resultat){
        $presences=$this->repository->PresenceRepository()->findBy(['anne'=>$anne,'mois'=>$mois,'date'=>$date,'user'=>$id,'employe'=>$resultat->getId()]);
        if(!$presences){
            $absent[]=[
                'nom'=>$resultat->getNom(),
                'prenom'=>$resultat->getPrenom(),
                'contact'=>$resultat->getContact(),
                'email'=>$resultat->getEmail(),
                'travaille'=>$resultat->getTravaille()
            ];
        }
      }
    return $this->reponses->success($absent,count($absent),Messages::SUCCESS);
    }

    #[Route('/presence/{idUser}/{idPresence}', name: 'suprimerPresence', methods: 'DELETE')]
    public function suprimerPresence($idUser,$idPresence):Response
    {
        $presence=$this->repository->PresenceRepository()->findOneBy(['user'=>$idUser,'id'=>$idPresence]);

        $this->service->em()->remove($presence);
        $this->service->em()->flush();
        return $this->reponses->success(null,null,Messages::SUCCESS_DELETE);
    }
}