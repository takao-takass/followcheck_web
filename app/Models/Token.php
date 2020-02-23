<?php

namespace App\Models;

class Token
{
    public static $requireProps = [];
    
    public $signtext;
    public $user_id;
    public $ipaddress;
    public $expir_datetime;

}
