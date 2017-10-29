<?php

namespace App\Http\Controllers;

use App\Services\TestService;
use App\Token;
use App\User;
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

    public function index(){
        try{
            $parms=request()->input();
            $data=$this->test->getPost($parms['id']);
            return response()->json(['ok'=>true,'result'=>$data],200);
        }catch (Exception $e){
            return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }

    }
    public function like(){
        try{
            $parms=request()->input();
        $likes=$this->test->addLike($parms['id']);
        return response()->json(['ok'=>true,'likes'=>$likes]);}
        catch (Exception $e){
        return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }

    }
    public function disLike(){
        try{
            $parms=request()->input();
            $likes=$this->test->disLike($parms['id']);
            return response()->json(['ok'=>true,'likes'=>$likes]);}
        catch (Exception $e){
            return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }
    }
    public function cats(){
        try{
            $cats=$this->test->cats();
            return response()->json(['ok'=>true,'likes'=>$cats]);}
        catch (Exception $e){
            return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }
    }
    public function test(){
//        $parms=[];
//        $parms=request()->input();
//        $tokenId= Token::where('token',$parms['token'])->get();
////        $user=Token::find($tokenId)->user;
//
//        return response()->json($tokenId);
        $parms=[];
        $parms=request()->input();
        $tokenId= User::where('id',$parms['id'])->first();
//        $user=Token::find($tokenId)->user;

        return response()->json($tokenId->username);
    }
    public function getComments(){//ill work on its security later not now . for now i will just define the functions and take tests from them
        try{
            $parms=[];
            $parms=request()->input();
            $data=$this->test->getComments($parms['id'],$parms['offset']);
            return response()->json(['ok'=>true,'comments'=>$data]);}
        catch (Exception $e){
            return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }

    }
    protected function addComment(){
        $parms=[];
        $parms=request()->input();
        $targetId=isset($parms['target_id'])?$parms['target_id']:0;
        $data=$this->test->addComment($parms['user_id'],$parms['post_id'],$parms['comment_body'],$targetId);
        return response()->json(['ok'=>true,'comments'=>$data]);
}
    protected function jsonify($data){
        //a function to create better structured json response


    }

}
