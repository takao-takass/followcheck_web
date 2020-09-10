<?php
 
namespace App\Styles;

class AboutNum
{
    public $num;

    public function __construct($num){
        $this->num = $num;
    }
    
    /**
     * 数値を粗い表記にする
     */
    public function GetAboutNum(){
        
        $aboutnum = number_format($this->num);
        if($this->num > 9999){
            $aboutnum = (floor($this->num/1000)/10).'万';
        }

        return $aboutnum;
    }

}
