<?php
namespace app\Exceptions;

class ParamInvalidException extends \Exception 
{
    public $detail;
    public $params;

    public function __construct($m,$p){
        $this->code = 400;
        $this->message = 'パラメータが正しくありません。';
        $this->detail=$m;
        $this->params=$p;
      }
}
