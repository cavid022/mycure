<?php namespace App\Admin_auth;
use App\Models\User;
class Admin_auth {
    
    public function admin() {
        return User::find($_SESSION['admin']);
    }
    
    public function check() {
        return isset($_SESSION['admin']);
    }
    
    public function attempt($email, $password) {
        $admin = User::where('email', $email)->first();
        if (!$admin) {
            return false;
        }
        if($admin->role == 'admin'){
            if (password_verify($password, $admin->password)) {
                $_SESSION['admin'] = $admin->id;
                return true;
        }
        }
        return false;
    }
    
    public function logout() {
        unset($_SESSION['admin']);
    }
}