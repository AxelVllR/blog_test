<?php
namespace App\Globals;

class Treatment {


    public function getGet($key) {
        if(isset($key) && isset($_GET[$key])) {
            return $_GET[$key];
        }

        return null;
    }

    public function getPost($key) {
        if(isset($key) && isset($_POST[$key])) {
            return $_POST[$key];
        }

        return null;
    }

    public function getAllPosts(): array
    {
        return $_POST;
    }

}