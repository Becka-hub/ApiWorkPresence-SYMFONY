<?php
namespace App\Controller\Api\Secure;

use App\Entity\Employe;
use App\Service\Service;
use App\Shared\Messages;
use App\Shared\Reponses;
use App\Service\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'ctrl_employe')]
#[Security("is_granted('ROLE_USER')")]
class EmployeController extends AbstractController
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

    #[Route('/employe', name: 'ajouteEmploye', methods: 'POST')]
    public function ajouteEmploye():Response
    {
        $data=$this->service->json_decode();
        if(!isset($data->nom,$data->prenom,$data->contact,$data->email,$data->travaille,$data->photo,$data->idUser) || ($data->nom==="" || $data->prenom==="" || $data->contact==="" || $data->email==="" || $data->travaille==="" || $data->photo==="" || $data->idUser==="")){
            return $this->reponses->error(Messages::FORM_INVALID);
        }

        $employe=$this->repository->EmployeRepository()->findOneBy(['nom'=>$data->nom,'prenom'=>$data->prenom]);
        if($employe){
            return $this->reponses->error(Messages::EMPLOYE_EXIST);
        }

        $imageName = $this->service->fichier64($this->getParameter('brochures_directory_employe'), $data->photo,"employe");
        $imageUrl = '/uploads/employe/' . $imageName;

        $user=$this->repository->userRepository()->findOneById($data->idUser);

        $employes=new Employe();
        $employes->setNom($data->nom);
        $employes->setPrenom($data->prenom);
        $employes->setContact($data->contact);
        $employes->setEmail($data->email);
        $employes->setTravaille($data->travaille);
        $employes->setPhoto($imageName);
        $employes->setUser($user);
        $employes->setPhotoUrl($imageUrl);
        $this->service->em()->persist($employes);
        $this->service->em()->flush();

        return $this->reponses->success($employes->tojson(),1,Messages::SUCCESS_INSERT);
    }

    #[Route('/employe/{id}', name: 'afficheEmploye', methods: 'GET')]
    public function afficheEmploye($id):Response
    {
        $employes=$this->repository->EmployeRepository()->findByUser($id);
        
        return $this->reponses->success( array_map(function (Employe $employe) {
            return $employe->tojson();
        }, $employes),count($employes),Messages::SUCCESS);

    }

    #[Route('/employePagination/{page}/{id}', name: 'afficheEmployePagination', methods: 'GET')]
    public function afficheEmployePagination($page,$id,Request $request,PaginatorInterface $paginator):Response
    {
        $employes=$this->repository->EmployeRepository()->findBy(['user'=>$id],['id'=>'DESC']);

        $limit=5;
        $employeData = $paginator->paginate(
            array_map(function (Employe $employe) {
            return $employe->tojson();
        }, $employes),
            $request->query->getInt('page',$page),
            $limit
        );

        $totalPage=count($employes)/$limit;
        
        return $this->reponses->successPagination($employeData, count($employes),round($totalPage,0,PHP_ROUND_HALF_UP), Messages::SUCCESS);

    }


    #[Route('/searchEmploye/{id}/{prenom}', name: 'afficheSearchEmploye', methods: 'GET')]
    public function afficheSearchEmploye($id,$prenom):Response
    {
        $employes=$this->repository->EmployeRepository()->findBySearh($id,$prenom);
        
        return $this->reponses->success( array_map(function (Employe $employe) {
            return $employe->tojson();
        }, $employes),count($employes),Messages::SUCCESS);

    }
    
    #[Route('/oneEmploye/{id}', name: 'afficheOneEmploye', methods: 'GET')]
    public function afficheOneEmploye($id):Response
    {
        $employe=$this->repository->EmployeRepository()->findOneById($id);
        return $this->reponses->success( $employe->tojson(),1,Messages::SUCCESS);
    }

    #[Route('/employe/{id}', name: 'modifierEmploye', methods: 'PUT')]
    public function modifierEmploye($id):Response
    {
         $data=$this->service->json_decode();

        $employe=$this->repository->EmployeRepository()->findOneById($id);

        if(isset($data->photo)){
            $imageName = $this->service->fichier64($this->getParameter('brochures_directory_employe'), $data->photo);
            $imageUrl = '/uploads/employe/' . $imageName;
            $photo_encien=$employe->getPhoto();
            $nomPhoto = $this->getParameter('brochures_directory_employe').$photo_encien;
            if (file_exists($nomPhoto)) {
                unlink($nomPhoto);
            }
        }
        
        if(isset($data->nom)){
            $employe->setNom($data->nom);
        }
       
        if(isset($data->prenom) && $data->prenom!=="" ){
            $employe->setPrenom($data->prenom);
        }

        if(isset($data->contact) && $data->contact !==""){
            $employe->setContact($data->contact);
        }

        if(isset($data->email)  && $data->email!==""){
            $employe->setEmail($data->email);
        }
        
        if(isset($data->travaille) && $data->travaille!==""){
            $employe->setTravaille($data->travaille);
        }

        if(isset($data->photo)){
            $employe->setPhoto($imageName);
            $employe->setPhotoUrl($imageUrl);
        }
        $this->service->em()->persist($employe);
        $this->service->em()->flush();

        return $this->reponses->success($employe->tojson(),1,Messages::SUCCESS_UPDATE);


    }


    #[Route('/employe/{id}', name: 'deleteEmploye', methods: 'DELETE')]
    public function deleteEmploye($id):Response
    {
        $employe=$this->repository->EmployeRepository()->findOneById($id);
        $nomPhoto = $this->getParameter('brochures_directory_employe') . '/' . $employe->getPhoto();
            if (file_exists($nomPhoto)) {
                unlink($nomPhoto);
            }
        $this->service->em()->remove($employe);
        $this->service->em()->flush();
        return $this->reponses->success(null,null,Messages::SUCCESS_DELETE);
    }




}