<?php

namespace App\Models;

class UserEntry
{
    public static $requireProps = ['email','password','passwordcheck'];
    
    public $id;
    public $name;
    public $email;
    public $password;
    public $passwordcheck;

}
