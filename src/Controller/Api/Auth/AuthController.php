<?php
namespace App\Controller\Api\Auth;

use App\Entity\User;
use App\Service\Service;
use App\Shared\Messages;
use App\Shared\Reponses;
use App\Service\Repository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthController extends AbstractController
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

    #[Route('/inscription', name: 'inscription', methods: 'POST')]
    public function inscription(MailerInterface $mailer): Response
    {
        $data = $this->service->json_decode();
        if(!isset($data->nom,$data->prenom,$data->adresse,$data->email,$data->activiter,$data->photo,$data->utilisateur) || ($data->utilisateur ==="" || $data->nom==="" || $data->prenom==="" || $data->adresse===""||$data->email===""||$data->activiter===""||$data->photo==="")){
            return $this->reponses->error(Messages::FORM_INVALID);
        }
        $user = $this->repository->userRepository()->findOneByEmail($data->email);
        if ($user) {
            return $this->reponses->error(Messages::MAILUSED);
        }

        $imageName = $this->service->fichier64($this->getParameter('brochures_directory_user'), $data->photo,'user');
        $imageUrl = '/uploads/user/' . $imageName;

        $alphanumeric = '0123456789qwertyuiopasdfgh@013456789jklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM@';

        $password = substr(str_shuffle($alphanumeric),30 , 20 );


        $user = new User();
        $user->setNom($data->nom);
        $user->setPrenom($data->prenom);
        $user->setAdresse($data->adresse);
        $user->setEmail($data->email);
        $user->setActiviter($data->activiter);
        $user->setNomUtilisateur($data->utilisateur);
        $user->setPassword($this->service->hasher()->hashPassword($user, $password));
        $user->setRoles(["ROLE_USER"]);
        $user->setPhoto($imageName);
        $user->setPhotoUrl($imageUrl);

        $this->service->em()->persist($user);
        $this->service->em()->flush();

        $email = (new TemplatedEmail())
        ->from(new Address('MAMINIAINAZAIN@gmail.com', 'Beckas'))
        ->to($data->email)
        ->subject('Information d\'utilisateur')
        ->htmlTemplate('email.html.twig')
        ->context([
            'nom'=>$data->nom,
            'prenom'=>$data->prenom,
            'adresse_email'=>$data->email,
            'mdp'=>$password,
            'utilisateur'=>$data->utilisateur
        ]);

        $mailer->send($email);


        return $this->reponses->success(["user"=>$user->tojson(),"password"=>$password], 1, Messages::REGISTER_SUCCESS);
    }

    #[Route('/login', name: 'login', methods: 'POST')]
    public function login(JWTTokenManagerInterface $tokenManager): Response
    {
        $data = $this->service->json_decode();
        if(!isset($data->email,$data->password) || ($data->email==="" || $data->password==="")){
            return $this->reponses->error(Messages::FORM_INVALID);
        }
        $user = $this->repository->userRepository()->findOneByEmail($data->email);
        if (!$user) {
            return $this->reponses->error(Messages::USER_NOT_FOUND);
        }

        if (!$this->service->hasher()->isPasswordValid($user, $data->password)) {
            return $this->reponses->error(Messages::PASSWORD_WRONG);
        }

        return $this->reponses->successLogin($user->tojson(), 1, $tokenManager->create($user), Messages::SUCCESS);
    }

    #[Route('/changePassword', name: 'changePassword', methods: 'POST')]
    public function changePassword(MailerInterface $mailer):Response
    {
        $data = $this->service->json_decode();
        if(!isset($data->email,$data->utilisateur) || ($data->email==="" || $data->utilisateur=== "")){
            return $this->reponses->error(Messages::FORM_INVALID);
        }

        $utilisateur= $this->repository->userRepository()->findOneByNomUtilisateur($data->utilisateur);
        if(!$utilisateur){
            return $this->reponses->error(Messages::USER_NOT_FOUND);
        }

        $user= $this->repository->userRepository()->findOneByEmail($data->email);

        if(!$user){
            return $this->reponses->error(Messages::EMAIL_NOT_FOUND);
        }
        
        $alphanumeric = '0123456789qwertyuiopasdfgh@013456789jklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM@';

        $password = substr(str_shuffle($alphanumeric),30 , 20 );

        $user->setPassword($this->service->hasher()->hashPassword($user,$password));
        $this->service->em()->persist($user);
        $this->service->em()->flush();
        
        $email = (new TemplatedEmail())
        ->from(new Address('MAMINIAINAZAIN@gmail.com', 'Beckas'))
        ->to($data->email)
        ->subject('Nouvelle information d\'utilisateur')
        ->htmlTemplate('email.html.twig')
        ->context([
            'nom'=>$user->getNom(),
            'prenom'=>$user->getPrenom(),
            'adresse_email'=>$user->getEmail(),
            'mdp'=>$password,
            'utilisateur'=>$user->getNomUtilisateur()
        ]);

        $mailer->send($email);
        return $this->reponses->success(["user"=>$user->tojson(),"password"=>$password], 1, Messages::CHANGE_PASSWORD);
    }

}