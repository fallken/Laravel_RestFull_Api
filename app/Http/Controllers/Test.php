<?php

namespace App\Http\Controllers;

use App\Services\TestService;
use Illuminate\Http\Request;

class Test extends Controller
{
    //lets first make a service container and add it to the controller then we will move on and try one of the requests in former api and add
    //use  it one here and see the changes
    protected $test;//define a protected variable
    public function __construct(TestService $service)
    {
        $this->test=$service;

    }

    public function index($id){
        $data=$this->test->getPost($id);
        return response()->json($data);
    }
    public function test(){
        return 'its working';
    }
}
