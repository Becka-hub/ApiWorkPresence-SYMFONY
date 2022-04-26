<?php 
namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\EmployeRepository;
use App\Repository\PresenceRepository;
use App\Repository\JaimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Repository extends AbstractController
{
    private UserRepository $userRepository;
    private EmployeRepository $employeRepository;
    private PresenceRepository $presenceRepository;
    private JaimeRepository $jaimeRepository;

   
    public function __construct(UserRepository $userRepository,EmployeRepository $employeRepository,PresenceRepository $presenceRepository,JaimeRepository $jaimeRepository)
    {
        $this->userRepository=$userRepository;
        $this->presenceRepository=$presenceRepository;
        $this->employeRepository=$employeRepository;
        $this->jaimeRepository=$jaimeRepository;
    }

    public function userRepository()
    {
        return $this->userRepository;
    }

    public function PresenceRepository()
    {
        return $this->presenceRepository;
    }

    public function EmployeRepository()
    {
        return $this->employeRepository;
    }

    public function JaimeRepository()
    {
        return $this->jaimeRepository;
    }



}