<?php

namespace app\controllers\admin;

use ishop\base\Controller;

class AppController extends \ishop\base\Controller{
    
    public $layout = 'admin';
    
    public function __construct($route){
        parent::__construct($route);
        if(!\app\models\User::isAdmin() && $route['action'] != 'login-admin'){
            redirect(ADMIN . '/user/login-admin');//UserController::loginAdminAction
        }
        new \app\models\AppModel();
    }
    
    public function getRequestID($get = TRUE, $id = 'id'){
        if($get){
            $data = $_GET;
        }  else {
            $data = $_POST;
        }
        $id = !empty($data[$id]) ? (int)$data[$id] : NULL;
        if(!$id){
            throw new \Exception('Страница не найдена', '404');
        }
        return $id;
    }
    
}
