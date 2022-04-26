<?php
namespace App\Shared;
use App\Shared\Messages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Reponses extends AbstractController
{
    public function success($donner = null, $rows=null, array $message =  Messages::SUCCESS): Response
    {

        return $rows === null && $donner=== null ? $this->json([
            'status' => $message['status'],
            'title' => $message['title'],
            'message'=> $message['message'],
        ], $message['code']):
        $this->json([
            'status' => $message['status'],
            'title' => $message['title'],
            'message'=> $message['message'],
            'totalRows'=> $rows,
            'donner' => $donner,
        ], $message['code']);
    }
    public function successLogin($donner = null, $rows=null,$token=null, array $message =  Messages::SUCCESS): Response
    {

        return  $this->json([
            'status' => $message['status'],
            'title' => $message['title'],
            'message'=> $message['message'],
            'totalRows'=> $rows,
            'donner' => $donner,
            'token'=>$token
        ], $message['code']);
    }

    public function successPagination($donner = null, $rows=null,$totalPage=null, array $message =  Messages::SUCCESS): Response
    {

        return  $this->json([
            'status' => $message['status'],
            'title' => $message['title'],
            'message'=> $message['message'],
            'totalRows'=> $rows,
            'donner' => $donner,
            'totalPage'=>$totalPage
        ], $message['code']);
    }
    public function error(array $message =  Messages::ERROR):Response
    { 
        return $this->json([
            'status'=>$message['status'],
            'title'=>$message['title'],
            'message' => $message['message'],
        ],$message['code']);
    }
}