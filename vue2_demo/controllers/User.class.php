<?php
class User {
    public function __construct(){

    }

    public function dologin(){
        $db = new DB();
        $user = $_REQUEST['username'];
        $sql = "SELECT `password` FROM `accounts` WHERE `username`=:username";
        $data = $db->getOne($sql, array('username'=>$user));

        $password = $_REQUEST['password'];
        $hash_password = $data['password'];

        if(password_verify($password, $hash_password)){
            $_SESSION['user'] = $user;
            return array('status' => 'OK');
        }else{
            return array('status'=>'Failed');
        }
    }

    public function dologout(){
        if(!empty($_SESSION)){
            session_destroy();
        }
        return array('status' => 'OK');
    }

    public function loginVerify(){
        $user = $_REQUEST['username'];
        if(array_key_exists('user', $_SESSION) && $_SESSION['user'] == $user){
            return array('status' => 'OK');
        }
        return array('status'=>'Failed');
    }
}
