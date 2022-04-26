<?php
namespace App\Service;

use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Service  extends AbstractController
{
    private UserPasswordHasherInterface $hasher;
    private ManagerRegistry $managerRegistry;

    public function __construct(UserPasswordHasherInterface $hasher, ManagerRegistry $managerRegistry)
    {
        $this->hasher = $hasher;
        $this->managerRegistry = $managerRegistry;
    }

    public function json_decode()
    {
        try {
            return file_get_contents('php://input') ?
                json_decode(file_get_contents('php://input')) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function em()
    {
        return $this->managerRegistry->getManager();
    }

    public function hasher()
    {
        return $this->hasher;
    }

    public function fichier64($brochure,$dataBase64)
    {
        $imageName = time().'.'.'png';
        $fileName = $brochure.$imageName;
        $file = fopen($fileName, 'wb');
        $data = explode(',',$dataBase64);
        fwrite($file, base64_decode(count($data) === 2 ? $data[1] : $data[0]));
        fclose($file);
        return $imageName;

    }


}