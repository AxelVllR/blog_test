<?php
namespace App\Globals;

class Session {

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function getAll() {
        if(isset($_SESSION) && !empty($_SESSION)) {
            return $_SESSION;
        }
    }

    public function get($key) {
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

}