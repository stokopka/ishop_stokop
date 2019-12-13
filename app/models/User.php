<?php

namespace app\models;

class User extends AppModel {
    
    public $attributes = [//можна добавлять значения по умолчаниЮ, сюда передаются данные с формы
        'login' => '',
        'password' => '',
        'name' => '',
        'email' => '',
        'address' => '',
    ];
    
    public $rules = [
        'required' => [
            ['login'],
            ['password'],
            ['name'],
            ['email'],
            ['address'],
        ],
        'email' =>[
            ['email'],
        ],
        'lengthMin' => [
            ['password', 6],
        ]
    ];
    
    public function checkUnique(){
        $user = \R::findOne('user', 'login = ? OR email = ?', [$this->attributes['login'], $this->attributes['email']]);
        if($user){
            if($user->login == $this->attributes['login']){
                $this->errors['unique'][] = 'Этот логин уже занят';
            }
            if($user->email == $this->attributes['email']){
                $this->errors['unique'][] = 'Этот email уже занят';
            }
            return false;
        }
        return true;
    }
    
    public function login($isAdmin = false){
        $login = !empty(trim($_POST['login'])) ? trim($_POST['login']) : null;
        $password = !empty(trim($_POST['password'])) ? trim($_POST['password']) : null;
        if($login && $password){//если есть и логин и пароль
            if($isAdmin){
                $user = \R::findOne('user', "login = ? AND role = 'admin'", [$login]);//проверка на админа
            }else{
                $user = \R::findOne('user', "login = ?", [$login]);//просто пользователь
            }
            if($user){
                if(password_verify($password, $user->password)){//проверяем пароль(если пароли из формы и из таблицы совпадут)
                    foreach($user as $k => $v){
                        if($k != 'password') $_SESSION['user'][$k] = $v;//положим все, кроме пароля
                    }
                    return true;
                }
            }
        }
        return false;//пустые, либо нет такого пользователя с таким паролем
    }

    public static function checkAuth(){
        return isset($_SESSION['user']);//авторизован пользователь или нет?
    }
    
    public static function isAdmin(){//true если админ
        return (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin');
    }
}
