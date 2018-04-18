<?php namespace App\Admin_auth;
use App\Models\User;
class Remember_me {
    
    public function checkEmail() {
        if(isset($_COOKIE['email'])){
            return $_COOKIE['email'];
        }else{
            return 'error';
        }
    }
    public function checkPassword() {
        if(isset($_COOKIE['password'])){
            return $_COOKIE['password'];
        }else{
            return 'error';
        }
    }
    public function checkRememberMe() {
        if(isset($_COOKIE['remember_me'])){
            return 'checked';
        }else{
            return ' ';
        }
    }
    
    public function attempt($email, $password, $remember_me) {
        if(isset($remember_me)){
                setcookie('email',$email,time() + (86400 * 7));
                setcookie('password',$password,time() + (86400 * 7));
                setcookie('remember_me',$remember_me,time() + (86400 * 7));
                
            return true;
        }else{
                if(isset($_COOKIE[email])){
                     setcookie("email", "");
                }
                    if(isset($_COOKIE['password'])){
                         setcookie("password", "");
                    }
                        if(isset($_COOKIE['remember_me'])){
                             setcookie("remember_me", "");
                        }
            return false;
        }
        
    }
    
}