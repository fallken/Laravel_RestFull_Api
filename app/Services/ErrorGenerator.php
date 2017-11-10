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
        9=>'data not found',
        10=>'user already exists',
        11=>'email is not valid',
        12=>'there is an account already  registered with same email',
        13=>'user registration process face an issue',
        14=>'username or password is not set pls try again',
        15=>'username not registered',
        16=>'user creditentials are not correct',
        17=>'no such user were found using this token',
        18=>'token not set or empty',
        19=>'user logged out successfully',
        20=>'something went wrong deleting the user',
        21=>'token is not valid',
    ];
public function errorMaker($stat,$data,$errNo){//craeting typical error structure to fullfil the needs of current api classes?
    if ($stat==0){
    return response()->json(['ok'=>false , "error"=>["code"=>$errNo,"text"=>$this->errorList[$errNo]]]);
    }
    if ($stat==2){
        return response()->json(['ok'=>false , "error"=>["text"=>$data]]);
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