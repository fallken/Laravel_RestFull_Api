<?php

namespace App\Http\Controllers\v1;

use App\Services\OtherService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class Other extends Controller
{

    protected $service;
    public function __construct(OtherService $service)
    {
        $this->service=$service;
    }//i need to make jsonify func a global function to stop writing the same code again and again ..
    public function sendBug(){
        $parms=[];
        $parms=request()->input();
        if (isset($parms['name'])&&isset($parms['email'])&&isset($parms['mobile_details'])&&isset($parms['text'])&& !empty($parms['name'])&&
            !empty($parms['name'])&& !empty($parms['mobile_details'])&& !empty($parms['text'])){
            if ($this->service->addBug($parms['name'],$parms['email'],$parms['mobile_details'],$parms['text']))
                return $this->jsonify(2,'bug has been sent');
            }
            else
                return $this->jsonify(2,'there was a problem adding the bug into database');
    }
    public function lastVersion(){//its not operatable right now
        $ver = file_get_contents(DIR."version.txt");//need to specify the direction
        return $this->jsonify(2,['last_version'=>$ver]);
    }


    protected function jsonify($stat=0,$data=0,$errNo=null){
        //a function to create better structured json response
        $err= app('ErrorGen');//this will load the class in the services foler in App directory named ErrorGenerator
        return $err->errorMaker($stat,$data,$errNo);

    }

}
