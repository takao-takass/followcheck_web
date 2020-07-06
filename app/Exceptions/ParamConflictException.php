<?php
namespace app\Exceptions;

class ParamConflictException extends \Exception 
{
    public $code;
    public $detail;
    public $params;

    public function __construct($m,$p){
        $this->code = 409;
        $this->message = 'データの競合が発生しました。';
        $this->detail=$m;
        $this->params=$p;
      }
}
