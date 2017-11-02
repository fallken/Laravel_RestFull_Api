<?php
/**
 * Created by PhpStorm.
 * User: Elomir
 * Date: 11/1/2017
 * Time: 9:50 PM
 */

namespace App\Services;


class ErrorGenerator
{
    protected $errorList=[
        1=>'data not found',
        2=>'could not connect to server',
        3=>'there was a problem loading the data',
        4=>'the input should be integer',
        5=>'the requested string length is less that than required length',
        6=>'the process was successful',
        7=>'there was a problem in the application',
        8=>'cannot accept empty input for word',
        9=>'data not found'
    ];
public function errorMaker($stat,$data,$errNo){//craeting typical error structure to fullfil the needs of current api classes?
    if ($stat==0){
    return response()->json(['ok'=>false , "error"=>["code"=>$errNo,"text"=>$this->errorList[$errNo]]]);
    }
    elseif($stat==1){
        if (is_numeric($data)&&$data==0){
            return response()->json(['ok'=>true,"result"=>["code"=>$errNo,"text"=>$this->errorList[$errNo]]]);
        }
        return response()->json(['ok'=>true,"result"=>[$data]]);
    }
    else{
        return response()->json(['ok'=>false,"error"=>["code"=>$this->errorList[7]]]);
    }
}
}