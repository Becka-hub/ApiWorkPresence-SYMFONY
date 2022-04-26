<?php
namespace App\Shared;

class Messages 
{
    public const SUCCESS  = ['status' => true, 'title' => 'success','message'=>'success','code' => 200];
    public const SUCCESS_INSERT  = ['status' => true, 'title' => 'success','message'=>'Insertion avec success', 'code' => 200];
    public const SUCCESS_UPDATE  = ['status' => true, 'title' => 'success','message'=>'Modification avec success', 'code' => 200];
    public const SUCCESS_DELETE  = ['status' => true, 'title' => 'success','message'=>'Supression avec success', 'code' => 200];
    public const REGISTER_SUCCESS  = ['status' => true, 'title' => 'success','message'=>'Inscription avec success,voir votre boite email', 'code' => 200];
    public const ADDEMPLOYE_SUCCESS  = ['status' => true, 'title' => 'success','message'=>'Employé ajouter avec success', 'code' => 200];
    public const CHANGE_PASSWORD = ['status' => true, 'title' => 'success','message'=>'Mot de passe a bien changer,voir votre boite email', 'code' => 200];
    public const PRESENCE_SORTIE = ['status' => true, 'title' => 'success','message'=>'Présence compléter', 'code' => 200];
    public const JAIME = ['status' => true, 'title' => 'success','message'=>'Merci d\'avoir aimer l\'application', 'code' => 200];
    public const SUCCESS_PRESENCE = ['status' => true, 'title' => 'success','message'=>'Ajoute présence avec success', 'code' => 200];

    public const ERROR  = ['status' => false, 'title' => 'error','message'=>'error','code' => 400];
    public const FORM_INVALID= ['status' => false,'title'=>'error','message'=>'Quelque champ du formulaire est vide','code'=>400];
    public const EMPLOYE_NOT_FOUND= ['status' => false,'title'=>'error','message'=>'Employé n\'existe pas','code'=>404];
    public const EMPLOYE_EXIST= ['status' => false,'title'=>'error','message'=>'Employé déjà existé','code'=>400];
    public const USER_NOT_FOUND= ['status' => false,'title'=>'error','message'=>'Utilisateur n\'existe pas','code'=>404];
    public const EMAIL_NOT_FOUND= ['status' => false,'title'=>'error','message'=>'Adresse email n\'existe pas','code'=>404];
    public const MAILUSED = ['status'=>false,'title' => 'error', 'message' => 'Email déjà utilisé', 'code' => 400];
    public const PASSWORD_WRONG= ['status' => false,'title'=>'error','message'=>'Mot de passe incorrect','code'=>400];
    public const PRESENCE_EXIST= ['status' => false,'title'=>'error','message'=>'Cette employé est déjà présent','code'=>400];
    
}