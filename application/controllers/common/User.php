<?php

class User extends Library\MainController
{

    public function index()
    {
    }

    public function login() {
    }

    public function register() {

        if ($this->isPost()) {

        }
        else {
            die('Show form');
        }
    }
}